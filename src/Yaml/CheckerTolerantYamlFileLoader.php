<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Nette\Utils\Strings;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * The need: https://github.com/symfony/symfony/pull/21313#issuecomment-372037445
 */
final class CheckerTolerantYamlFileLoader extends FileLoader
{
    /**
     * @var YamlFileLoader
     */
    private $yamlFileLoader;

    public function __construct(ContainerBuilder $containerBuilder, FileLocatorInterface $fileLocator)
    {
        $this->yamlFileLoader = new YamlFileLoader($containerBuilder, $fileLocator);

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

            // encode to temp file
            $tempFile = __DIR__ . '/temp-file.yml';
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
                // move parameters to "configure()" call
                $yaml['services'][$checker]['calls'] = [
                    ['configure', [$serviceDefinition]],
                ];
            }

            if (Strings::endsWith($checker, 'Sniff')) {
                // move parameters to property setters
                foreach ($serviceDefinition as $key => $value) {
                    $yaml['services'][$checker]['properties'][$key] = $value;
                }
            }

            // cleanup parameters
            foreach ($serviceDefinition as $key => $value) {
                unset($yaml['services'][$checker][$key]);
            }
        }

        return $yaml;
    }
}
