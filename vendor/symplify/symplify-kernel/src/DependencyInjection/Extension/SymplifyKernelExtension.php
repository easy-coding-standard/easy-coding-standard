<?php

declare (strict_types=1);
namespace ECSPrefix20210715\Symplify\SymplifyKernel\DependencyInjection\Extension;

use ECSPrefix20210715\Symfony\Component\Config\FileLocator;
use ECSPrefix20210715\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210715\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210715\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SymplifyKernelExtension extends \ECSPrefix20210715\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     * @return void
     */
    public function load($configs, $containerBuilder)
    {
        $phpFileLoader = new \ECSPrefix20210715\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210715\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('common-config.php');
    }
}
