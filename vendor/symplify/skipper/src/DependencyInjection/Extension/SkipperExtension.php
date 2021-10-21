<?php

declare (strict_types=1);
namespace ECSPrefix20211021\Symplify\Skipper\DependencyInjection\Extension;

use ECSPrefix20211021\Symfony\Component\Config\FileLocator;
use ECSPrefix20211021\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211021\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20211021\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SkipperExtension extends \ECSPrefix20211021\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function load($configs, $containerBuilder) : void
    {
        // needed for parameter shifting of sniff/fixer params
        $phpFileLoader = new \ECSPrefix20211021\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20211021\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
