<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210825\Symfony\Component\HttpKernel\DependencyInjection;

use ECSPrefix20210825\Psr\Log\LoggerInterface;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210825\Symfony\Component\HttpKernel\Log\Logger;
/**
 * Registers the default logger if necessary.
 *
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class LoggerPass implements \ECSPrefix20210825\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    /**
     * {@inheritdoc}
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process($container)
    {
        $container->setAlias(\ECSPrefix20210825\Psr\Log\LoggerInterface::class, 'logger')->setPublic(\false);
        if ($container->has('logger')) {
            return;
        }
        $container->register('logger', \ECSPrefix20210825\Symfony\Component\HttpKernel\Log\Logger::class)->setPublic(\false);
    }
}
