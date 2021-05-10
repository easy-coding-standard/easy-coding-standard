<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\HttpKernel\DependencyInjection;

use ECSPrefix20210510\Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\Exception\RuntimeException;
use ECSPrefix20210510\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Alexander M. Turek <me@derrabus.de>
 */
class ResettableServicePass implements \ECSPrefix20210510\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $tagName;
    /**
     * @param string $tagName
     */
    public function __construct($tagName = 'kernel.reset')
    {
        $tagName = (string) $tagName;
        $this->tagName = $tagName;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (!$container->has('services_resetter')) {
            return;
        }
        $services = $methods = [];
        foreach ($container->findTaggedServiceIds($this->tagName, \true) as $id => $tags) {
            $services[$id] = new \ECSPrefix20210510\Symfony\Component\DependencyInjection\Reference($id, \ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerInterface::IGNORE_ON_UNINITIALIZED_REFERENCE);
            foreach ($tags as $attributes) {
                if (!isset($attributes['method'])) {
                    throw new \ECSPrefix20210510\Symfony\Component\DependencyInjection\Exception\RuntimeException(\sprintf('Tag "%s" requires the "method" attribute to be set.', $this->tagName));
                }
                if (!isset($methods[$id])) {
                    $methods[$id] = [];
                }
                $methods[$id][] = $attributes['method'];
            }
        }
        if (!$services) {
            $container->removeAlias('services_resetter');
            $container->removeDefinition('services_resetter');
            return;
        }
        $container->findDefinition('services_resetter')->setArgument(0, new \ECSPrefix20210510\Symfony\Component\DependencyInjection\Argument\IteratorArgument($services))->setArgument(1, $methods);
    }
}
