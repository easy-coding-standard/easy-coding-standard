<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\DependencyInjection\Extension\CheckersExtensionGuardian;

/**
 * The need: https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
final class CheckerTolerantYamlFileLoader extends FileLoader
{
    /**
     * @var YamlFileLoader
     */
    private $yamlFileLoader;

    /**
     * @var CheckersExtensionGuardian
     */
    private $checkersExtensionGuardian;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->yamlFileLoader = new YamlFileLoader($containerBuilder, $fileLocator);
        $this->checkersExtensionGuardian = new CheckersExtensionGuardian();

        parent::__construct($containerBuilder, $fileLocator);
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function load($resource, $type = null): void
    {
        $decodedYaml = Yaml::parseFile($resource);

        if (isset($decodedYaml['services'])) {
            $decodedYaml = $this->moveArgumentsToPropertiesOrMethodCalls($decodedYaml);

            // encode to temp file, have to be stored in same directory as original due to relative paths in "imports"
            $tempFile = $resource . '-tuned.yml';
            file_put_contents($tempFile, Yaml::dump($decodedYaml));

            $resource = $tempFile;
        }

        $this->yamlFileLoader->load($resource, $type);

        // cleanup temp file
        if (isset($tempFile)) {
            unlink($tempFile);
        }
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     */
    public function supports($resource, $type = null): bool
    {
        return $this->yamlFileLoader->supports($resource, $type);
    }

    public function getResolver(): LoaderResolverInterface
    {
        return $this->yamlFileLoader->getResolver();
    }

    public function setResolver(LoaderResolverInterface $loaderResolver): void
    {
        $this->yamlFileLoader->setResolver($loaderResolver);
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
                $this->checkersExtensionGuardian->ensureFixerIsConfigurable($checker, $serviceDefinition);
                // move parameters to "configure()" call
                $yaml['services'][$checker]['calls'] = [
                    ['configure', [$serviceDefinition]],
                ];
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                // move parameters to property setters
                foreach ($serviceDefinition as $key => $value) {
                    $this->checkersExtensionGuardian->ensurePropertyExists($checker, $key);
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
