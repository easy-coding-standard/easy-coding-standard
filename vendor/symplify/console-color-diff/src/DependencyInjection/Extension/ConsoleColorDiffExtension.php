<?php

declare (strict_types=1);
namespace ECSPrefix20211001\Symplify\ConsoleColorDiff\DependencyInjection\Extension;

use ECSPrefix20211001\Symfony\Component\Config\FileLocator;
use ECSPrefix20211001\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20211001\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20211001\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class ConsoleColorDiffExtension extends \ECSPrefix20211001\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function load($configs, $containerBuilder) : void
    {
        $phpFileLoader = new \ECSPrefix20211001\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20211001\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
