<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * The need: https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
final class CheckerTolerantYamlFileLoader extends YamlFileLoader
{
    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->checkerConfigurationGuardian = new CheckerConfigurationGuardian();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param string $file
     * @return array|mixed|mixed[]
     */
    protected function loadFile($file)
    {
        $decodedYaml = parent::loadFile($file);

        if (isset($decodedYaml['services'])) {
            return $this->moveArgumentsToPropertiesOrMethodCalls($decodedYaml);
        }

        return $decodedYaml;
    }

    /**
     * @param mixed[] $yaml
     * @return mixed[]
     */
    private function moveArgumentsToPropertiesOrMethodCalls(array $yaml): array
    {
        foreach ($yaml['services'] as $checker => $serviceDefinition) {
            if (empty($serviceDefinition)) {
                continue;
            }

            // is checker service?
            if (! Strings::endsWith($checker, 'Fixer') && ! Strings::endsWith($checker, 'Sniff')) {
                continue;
            }

            if (Strings::endsWith($checker, 'Fixer')) {
                $this->checkerConfigurationGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);
                // move parameters to "configure()" call
                $yaml['services'][$checker]['calls'] = [
                    ['configure', [$serviceDefinition]],
                ];
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                // move parameters to property setters
                foreach ($serviceDefinition as $key => $value) {
                    $this->checkerConfigurationGuardian->ensurePropertyExists($checker, $key);
                    $yaml['services'][$checker]['properties'][$key] = $this->escapeValue($value);
                }
            }

            // cleanup parameters
            foreach ($serviceDefinition as $key => $value) {
                unset($yaml['services'][$checker][$key]);
            }
        }

        return $yaml;
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function escapeValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        return Strings::replace($value, '#@#', '@@');
    }
}
