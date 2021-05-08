<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use ECSPrefix20210508\Symfony\Component\ExpressionLanguage\Expression;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ContainerConfigurator extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'container';
    private $container;
    private $loader;
    private $instanceof;
    private $path;
    private $file;
    private $anonymousCount = 0;
    /**
     * @param string $path
     */
    public function __construct(\ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerBuilder $container, \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\PhpFileLoader $loader, array &$instanceof, $path, string $file)
    {
        if (\is_object($path)) {
            $path = (string) $path;
        }
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof =& $instanceof;
        $this->path = $path;
        $this->file = $file;
    }
    /**
     * @param string $namespace
     */
    public final function extension($namespace, array $config)
    {
        if (\is_object($namespace)) {
            $namespace = (string) $namespace;
        }
        if (!$this->container->hasExtension($namespace)) {
            $extensions = \array_filter(\array_map(function (\ECSPrefix20210508\Symfony\Component\DependencyInjection\Extension\ExtensionInterface $ext) {
                return $ext->getAlias();
            }, $this->container->getExtensions()));
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $this->file, $namespace, $extensions ? \implode('", "', $extensions) : 'none'));
        }
        $this->container->loadFromExtension($namespace, static::processValue($config));
    }
    /**
     * @param string $resource
     */
    public final function import($resource, string $type = null, $ignoreErrors = \false)
    {
        if (\is_object($resource)) {
            $resource = (string) $resource;
        }
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator
     */
    public final function parameters()
    {
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator($this->container);
    }
    /**
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator
     */
    public final function services()
    {
        return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator($this->container, $this->loader, $this->instanceof, $this->path, $this->anonymousCount);
    }
    /**
     * @return static
     * @param string $path
     */
    public final function withPath($path)
    {
        if (\is_object($path)) {
            $path = (string) $path;
        }
        $clone = clone $this;
        $clone->path = $clone->file = $path;
        return $clone;
    }
}
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
\class_alias('ECSPrefix20210508\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator', 'Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator', \false);
/**
 * Creates a parameter.
 * @param string $name
 */
function param($name) : string
{
    if (\is_object($name)) {
        $name = (string) $name;
    }
    return '%' . $name . '%';
}
/**
 * Creates a service reference.
 *
 * @deprecated since Symfony 5.1, use service() instead.
 * @param string $id
 */
function ref($id) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
{
    if (\is_object($id)) {
        $id = (string) $id;
    }
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "service()" instead.', __FUNCTION__);
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator($id);
}
/**
 * Creates a reference to a service.
 * @param string $serviceId
 */
function service($serviceId) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
{
    if (\is_object($serviceId)) {
        $serviceId = (string) $serviceId;
    }
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator($serviceId);
}
/**
 * Creates an inline service.
 *
 * @deprecated since Symfony 5.1, use inline_service() instead.
 * @param string $class
 */
function inline($class = null) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
{
    if (\is_object($class)) {
        $class = (string) $class;
    }
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "inline_service()" instead.', __FUNCTION__);
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator(new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition($class));
}
/**
 * Creates an inline service.
 * @param string $class
 */
function inline_service($class = null) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
{
    if (\is_object($class)) {
        $class = (string) $class;
    }
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator(new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition($class));
}
/**
 * Creates a service locator.
 *
 * @param ReferenceConfigurator[] $values
 * @return \Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
 */
function service_locator(array $values)
{
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(\ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator.
 *
 * @param ReferenceConfigurator[] $values
 * @return \Symfony\Component\DependencyInjection\Argument\IteratorArgument
 */
function iterator(array $values)
{
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\IteratorArgument(\ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator::processValue($values, \true));
}
/**
 * Creates a lazy iterator by tag name.
 * @param string $tag
 */
function tagged_iterator($tag, string $indexAttribute = null, string $defaultIndexMethod = null, string $defaultPriorityMethod = null) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument
{
    if (\is_object($tag)) {
        $tag = (string) $tag;
    }
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \false, $defaultPriorityMethod);
}
/**
 * Creates a service locator by tag name.
 * @param string $tag
 */
function tagged_locator($tag, string $indexAttribute = null, string $defaultIndexMethod = null) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
{
    if (\is_object($tag)) {
        $tag = (string) $tag;
    }
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument(new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, \true));
}
/**
 * Creates an expression.
 * @param string $expression
 */
function expr($expression) : \ECSPrefix20210508\Symfony\Component\ExpressionLanguage\Expression
{
    if (\is_object($expression)) {
        $expression = (string) $expression;
    }
    return new \ECSPrefix20210508\Symfony\Component\ExpressionLanguage\Expression($expression);
}
/**
 * Creates an abstract argument.
 * @param string $description
 */
function abstract_arg($description) : \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\AbstractArgument
{
    if (\is_object($description)) {
        $description = (string) $description;
    }
    return new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Argument\AbstractArgument($description);
}
