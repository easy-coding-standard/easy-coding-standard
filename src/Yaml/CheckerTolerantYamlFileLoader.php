<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

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
        // @todo magic happens here
        $this->yamlFileLoader->load($resource, $type);
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
}
