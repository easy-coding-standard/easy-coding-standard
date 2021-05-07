<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
abstract class AbstractServiceConfigurator extends \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    protected $parent;
    protected $id;
    private $defaultTags = [];
    /**
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\Definition $definition
     * @param string $id
     */
    public function __construct($parent, $definition, $id = null, array $defaultTags = [])
    {
        $this->parent = $parent;
        $this->definition = $definition;
        $this->id = $id;
        $this->defaultTags = $defaultTags;
    }
    public function __destruct()
    {
        // default tags should be added last
        foreach ($this->defaultTags as $name => $attributes) {
            foreach ($attributes as $attributes) {
                $this->definition->addTag($name, $attributes);
            }
        }
        $this->defaultTags = [];
    }
    /**
     * Registers a service.
     * @param string|null $id
     * @param string $class
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    public final function set($id, $class = null)
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
    /**
     * Creates an alias.
     * @param string $id
     * @param string $referencedId
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
     */
    public final function alias($id, $referencedId)
    {
        $this->__destruct();
        return $this->parent->alias($id, $referencedId);
    }
    /**
     * Registers a PSR-4 namespace using a glob pattern.
     * @param string $namespace
     * @param string $resource
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator
     */
    public final function load($namespace, $resource)
    {
        $this->__destruct();
        return $this->parent->load($namespace, $resource);
    }
    /**
     * Gets an already defined service definition.
     *
     * @throws ServiceNotFoundException if the service definition does not exist
     * @param string $id
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    public final function get($id)
    {
        $this->__destruct();
        return $this->parent->get($id);
    }
    /**
     * Registers a stack of decorator services.
     *
     * @param InlineServiceConfigurator[]|ReferenceConfigurator[] $services
     * @param string $id
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
     */
    public final function stack($id, array $services)
    {
        $this->__destruct();
        return $this->parent->stack($id, $services);
    }
    /**
     * Registers a service.
     * @param string $id
     * @param string $class
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    public final function __invoke($id, $class = null)
    {
        $this->__destruct();
        return $this->parent->set($id, $class);
    }
}
