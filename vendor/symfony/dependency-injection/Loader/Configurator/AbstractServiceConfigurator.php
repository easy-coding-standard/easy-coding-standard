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

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

abstract class AbstractServiceConfigurator extends AbstractConfigurator
{
    protected $parent;
    protected $id;
    private $defaultTags = [];

    /**
     * @param string $id
     */
    public function __construct(ServicesConfigurator $parent, Definition $definition, $id = null, array $defaultTags = [])
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
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    final public function set($id, $class = null)
    {
        $this->__destruct();

        return $this->parent->set($id, $class);
    }

    /**
     * Creates an alias.
     * @param string $id
     * @param string $referencedId
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
     */
    final public function alias($id, $referencedId)
    {
        $id = (string) $id;
        $referencedId = (string) $referencedId;
        $this->__destruct();

        return $this->parent->alias($id, $referencedId);
    }

    /**
     * Registers a PSR-4 namespace using a glob pattern.
     * @param string $namespace
     * @param string $resource
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\PrototypeConfigurator
     */
    final public function load($namespace, $resource)
    {
        $namespace = (string) $namespace;
        $resource = (string) $resource;
        $this->__destruct();

        return $this->parent->load($namespace, $resource);
    }

    /**
     * Gets an already defined service definition.
     *
     * @throws ServiceNotFoundException if the service definition does not exist
     * @param string $id
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    final public function get($id)
    {
        $id = (string) $id;
        $this->__destruct();

        return $this->parent->get($id);
    }

    /**
     * Registers a stack of decorator services.
     *
     * @param InlineServiceConfigurator[]|ReferenceConfigurator[] $services
     * @param string $id
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\AliasConfigurator
     */
    final public function stack($id, array $services)
    {
        $id = (string) $id;
        $this->__destruct();

        return $this->parent->stack($id, $services);
    }

    /**
     * Registers a service.
     * @param string $id
     * @param string $class
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\ServiceConfigurator
     */
    final public function __invoke($id, $class = null)
    {
        $id = (string) $id;
        $this->__destruct();

        return $this->parent->set($id, $class);
    }
}
