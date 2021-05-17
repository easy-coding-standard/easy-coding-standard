<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210517\Symfony\Component\DependencyInjection\LazyProxy\Instantiator;

use ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210517\Symfony\Component\DependencyInjection\Definition;
/**
 * {@inheritdoc}
 *
 * Noop proxy instantiator - produces the real service instead of a proxy instance.
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 */
class RealServiceInstantiator implements \ECSPrefix20210517\Symfony\Component\DependencyInjection\LazyProxy\Instantiator\InstantiatorInterface
{
    /**
     * {@inheritdoc}
     * @param string $id
     */
    public function instantiateProxy(\ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerInterface $container, \ECSPrefix20210517\Symfony\Component\DependencyInjection\Definition $definition, $id, callable $realInstantiator)
    {
        $id = (string) $id;
        return $realInstantiator();
    }
}
