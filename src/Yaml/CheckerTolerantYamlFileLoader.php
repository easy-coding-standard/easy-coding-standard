<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use ReflectionClass;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * The need: https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
final class CheckerTolerantYamlFileLoader extends YamlFileLoader
{
    /**
     * @var string
     */
    private const SERVICES_KEY = 'services';

    /**
     * @var string
     */
    private const CALLS_KEY = 'calls';

    /**
     * @var string
     */
    private const PROPERTIES_KEY = 'properties';

    /**
     * @var CheckerConfigurationGuardian
     */
    private $checkerConfigurationGuardian;

    /**
     * @var string[]
     */
    private $serviceKeywords = [];

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

        if (isset($decodedYaml[self::SERVICES_KEY])) {
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
        foreach ($yaml[self::SERVICES_KEY] as $checker => $serviceDefinition) {
            if (empty($serviceDefinition)) {
                continue;
            }

            if (! $this->isCheckersClass($checker)) {
                continue;
            }

            if (Strings::endsWith($checker, 'Fixer')) {
                $yaml = $this->processFixer($yaml, $checker, $serviceDefinition);
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                $yaml = $this->processSniff($yaml, $checker, $serviceDefinition);
            }

            // cleanup parameters
            foreach ($serviceDefinition as $key => $value) {
                if ($this->isReservedKey($key)) {
                    continue;
                }

                unset($yaml[self::SERVICES_KEY][$checker][$key]);
            }
        }

        return $yaml;
    }

    private function isCheckersClass($checker): bool
    {
        return Strings::endsWith($checker, 'Fixer') || Strings::endsWith($checker, 'Sniff');
    }

    /**
     * @param mixed[] $yaml
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processFixer(array $yaml, string $checker, array $serviceDefinition): array
    {
        $this->checkerConfigurationGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);
        // move parameters to "configure()" call
        $yaml[self::SERVICES_KEY][$checker][self::CALLS_KEY] = [
            ['configure', [$serviceDefinition]],
        ];

        return $yaml;
    }

    /**
     * @param mixed[] $yaml
     * @param mixed[] $serviceDefinition
     * @return mixed[]
     */
    private function processSniff(array $yaml, string $checker, array $serviceDefinition): array
    {
        // move parameters to property setters
        foreach ($serviceDefinition as $key => $value) {
            if ($this->isReservedKey($key)) {
                continue;
            }

            $this->checkerConfigurationGuardian->ensurePropertyExists($checker, $key);
            $yaml[self::SERVICES_KEY][$checker][self::PROPERTIES_KEY][$key] = $this->escapeValue($value);
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

    private function isReservedKey(string $key): bool
    {
        return in_array($key, $this->getServiceKeywords(), true);
    }

    /**
     * @return string[]
     */
    private function getServiceKeywords(): array
    {
        if ($this->serviceKeywords) {
            return $this->serviceKeywords;
        }

        $reflectionClass = new ReflectionClass(YamlFileLoader::class);

        return $this->serviceKeywords = $reflectionClass->getStaticProperties()['serviceKeywords'];
    }
}
