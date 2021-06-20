<?php

declare (strict_types=1);
namespace ECSPrefix20210620\Symplify\Skipper\DependencyInjection\Extension;

use ECSPrefix20210620\Symfony\Component\Config\FileLocator;
use ECSPrefix20210620\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210620\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210620\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SkipperExtension extends \ECSPrefix20210620\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @return void
     */
    public function load(array $configs, \ECSPrefix20210620\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        // needed for parameter shifting of sniff/fixer params
        $phpFileLoader = new \ECSPrefix20210620\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210620\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
