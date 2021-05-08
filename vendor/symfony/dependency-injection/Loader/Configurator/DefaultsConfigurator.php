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

use ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class DefaultsConfigurator extends \ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator
{
    const FACTORY = 'defaults';
    use Traits\AutoconfigureTrait;
    use Traits\AutowireTrait;
    use Traits\BindTrait;
    use Traits\PublicTrait;
    private $path;
    /**
     * @param string $path
     */
    public function __construct(\ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent, \ECSPrefix20210508\Symfony\Component\DependencyInjection\Definition $definition, $path = null)
    {
        parent::__construct($parent, $definition, null, []);
        $this->path = $path;
    }
    /**
     * Adds a tag for this definition.
     *
     * @return $this
     *
     * @throws InvalidArgumentException when an invalid tag name or attribute is provided
     * @param string $name
     */
    public final function tag($name, array $attributes = [])
    {
        $name = (string) $name;
        if ('' === $name) {
            throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException('The tag name in "_defaults" must be a non-empty string.');
        }
        foreach ($attributes as $attribute => $value) {
            if (null !== $value && !\is_scalar($value)) {
                throw new \ECSPrefix20210508\Symfony\Component\DependencyInjection\Exception\InvalidArgumentException(\sprintf('Tag "%s", attribute "%s" in "_defaults" must be of a scalar-type.', $name, $attribute));
            }
        }
        $this->definition->addTag($name, $attributes);
        return $this;
    }
    /**
     * Defines an instanceof-conditional to be applied to following service definitions.
     * @param string $fqcn
     * @return \Symfony\Component\DependencyInjection\Loader\Configurator\InstanceofConfigurator
     */
    public final function instanceof($fqcn)
    {
        $fqcn = (string) $fqcn;
        return $this->parent->instanceof($fqcn);
    }
}
