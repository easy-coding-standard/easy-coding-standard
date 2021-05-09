<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument;
use Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ContainerConfigurator extends AbstractConfigurator
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
     * @param string $file
     */
    public function __construct(ContainerBuilder $container, PhpFileLoader $loader, array &$instanceof, $path, $file)
    {
        $path = (string) $path;
        $file = (string) $file;
        $this->container = $container;
        $this->loader = $loader;
        $this->instanceof = &$instanceof;
        $this->path = $path;
        $this->file = $file;
    }

    /**
     * @param string $namespace
     */
    final public function extension($namespace, array $config)
    {
        $namespace = (string) $namespace;
        if (!$this->container->hasExtension($namespace)) {
            $extensions = array_filter(array_map(function (ExtensionInterface $ext) { return $ext->getAlias(); }, $this->container->getExtensions()));
            throw new InvalidArgumentException(sprintf('There is no extension able to load the configuration for "%s" (in "%s"). Looked for namespace "%s", found "%s".', $namespace, $this->file, $namespace, $extensions ? implode('", "', $extensions) : 'none'));
        }

        $this->container->loadFromExtension($namespace, static::processValue($config));
    }

    /**
     * @param string $resource
     * @param string $type
     */
    final public function import($resource, $type = null, $ignoreErrors = false)
    {
        $resource = (string) $resource;
        $this->loader->setCurrentDir(\dirname($this->path));
        $this->loader->import($resource, $type, $ignoreErrors, $this->file);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ParametersConfigurator
     */
    final public function parameters()
    {
        return new ParametersConfigurator($this->container);
    }

    /**
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator
     */
    final public function services()
    {
        return new ServicesConfigurator($this->container, $this->loader, $this->instanceof, $this->path, $this->anonymousCount);
    }

    /**
     * @return static
     * @param string $path
     */
    final public function withPath($path)
    {
        $path = (string) $path;
        $clone = clone $this;
        $clone->path = $clone->file = $path;

        return $clone;
    }
}

/**
 * Creates a parameter.
 * @param string $name
 * @return string
 */
function param($name)
{
    $name = (string) $name;
    return '%'.$name.'%';
}

/**
 * Creates a service reference.
 *
 * @deprecated since Symfony 5.1, use service() instead.
 * @param string $id
 * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
 */
function ref($id)
{
    $id = (string) $id;
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "service()" instead.', __FUNCTION__);

    return new ReferenceConfigurator($id);
}

/**
 * Creates a reference to a service.
 * @param string $serviceId
 * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ReferenceConfigurator
 */
function service($serviceId)
{
    $serviceId = (string) $serviceId;
    return new ReferenceConfigurator($serviceId);
}

/**
 * Creates an inline service.
 *
 * @deprecated since Symfony 5.1, use inline_service() instead.
 * @param string $class
 * @return \Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
 */
function inline($class = null)
{
    $class = (string) $class;
    trigger_deprecation('symfony/dependency-injection', '5.1', '"%s()" is deprecated, use "inline_service()" instead.', __FUNCTION__);

    return new InlineServiceConfigurator(new Definition($class));
}

/**
 * Creates an inline service.
 * @param string $class
 * @return \Symfony\Component\DependencyInjection\Loader\Configurator\InlineServiceConfigurator
 */
function inline_service($class = null)
{
    $class = (string) $class;
    return new InlineServiceConfigurator(new Definition($class));
}

/**
 * Creates a service locator.
 *
 * @param ReferenceConfigurator[] $values
 * @return \Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
 */
function service_locator(array $values)
{
    return new ServiceLocatorArgument(AbstractConfigurator::processValue($values, true));
}

/**
 * Creates a lazy iterator.
 *
 * @param ReferenceConfigurator[] $values
 * @return \Symfony\Component\DependencyInjection\Argument\IteratorArgument
 */
function iterator(array $values)
{
    return new IteratorArgument(AbstractConfigurator::processValue($values, true));
}

/**
 * Creates a lazy iterator by tag name.
 * @param string $tag
 * @param string $indexAttribute
 * @param string $defaultIndexMethod
 * @param string $defaultPriorityMethod
 * @return \Symfony\Component\DependencyInjection\Argument\TaggedIteratorArgument
 */
function tagged_iterator($tag, $indexAttribute = null, $defaultIndexMethod = null, $defaultPriorityMethod = null)
{
    $tag = (string) $tag;
    $indexAttribute = (string) $indexAttribute;
    $defaultIndexMethod = (string) $defaultIndexMethod;
    $defaultPriorityMethod = (string) $defaultPriorityMethod;
    return new TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, false, $defaultPriorityMethod);
}

/**
 * Creates a service locator by tag name.
 * @param string $tag
 * @param string $indexAttribute
 * @param string $defaultIndexMethod
 * @return \Symfony\Component\DependencyInjection\Argument\ServiceLocatorArgument
 */
function tagged_locator($tag, $indexAttribute = null, $defaultIndexMethod = null)
{
    $tag = (string) $tag;
    $indexAttribute = (string) $indexAttribute;
    $defaultIndexMethod = (string) $defaultIndexMethod;
    return new ServiceLocatorArgument(new TaggedIteratorArgument($tag, $indexAttribute, $defaultIndexMethod, true));
}

/**
 * Creates an expression.
 * @param string $expression
 * @return \Symfony\Component\ExpressionLanguage\Expression
 */
function expr($expression)
{
    $expression = (string) $expression;
    return new Expression($expression);
}

/**
 * Creates an abstract argument.
 * @param string $description
 * @return \Symfony\Component\DependencyInjection\Argument\AbstractArgument
 */
function abstract_arg($description)
{
    $description = (string) $description;
    return new AbstractArgument($description);
}
