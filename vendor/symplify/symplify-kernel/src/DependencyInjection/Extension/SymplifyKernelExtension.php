<?php

declare (strict_types=1);
namespace ECSPrefix20211010\Symplify\SymplifyKernel\DependencyInjection\Extension;

use ECSPrefix20211010\Symfony\Component\Config\FileLocator;
use ECSPrefix20211010\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211010\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20211010\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SymplifyKernelExtension extends \ECSPrefix20211010\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function load($configs, $containerBuilder) : void
    {
        $phpFileLoader = new \ECSPrefix20211010\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20211010\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('common-config.php');
    }
}
