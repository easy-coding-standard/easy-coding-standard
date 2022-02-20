<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20220220\Symfony\Component\DependencyInjection\Alias;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class AliasConfigurator extends \ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator
{
    use Traits\DeprecateTrait;
    use Traits\PublicTrait;
    public const FACTORY = 'alias';
    public function __construct(\ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent, \ECSPrefix20220220\Symfony\Component\DependencyInjection\Alias $alias)
    {
        $this->parent = $parent;
        $this->definition = $alias;
    }
}
