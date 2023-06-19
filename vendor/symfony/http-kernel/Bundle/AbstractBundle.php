<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\Bundle;

use ECSPrefix202306\Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Container;
use ECSPrefix202306\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
/**
 * A Bundle that provides configuration hooks.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 */
abstract class AbstractBundle extends Bundle implements ConfigurableExtensionInterface
{
    /**
     * @var string
     */
    protected $extensionAlias = '';
    public function configure(DefinitionConfigurator $definition) : void
    {
    }
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
    }
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder) : void
    {
    }
    public function getContainerExtension() : ?ExtensionInterface
    {
        if ('' === $this->extensionAlias) {
            $this->extensionAlias = Container::underscore(\preg_replace('/Bundle$/', '', $this->getName()));
        }
        return $this->extension = $this->extension ?? new BundleExtension($this, $this->extensionAlias);
    }
    /**
     * {@inheritdoc}
     */
    public function getPath() : string
    {
        if (null === $this->path) {
            $reflected = new \ReflectionObject($this);
            // assume the modern directory structure by default
            $this->path = \dirname($reflected->getFileName(), 2);
        }
        return $this->path;
    }
}
