<?php

declare (strict_types=1);
namespace ECSPrefix20210825\Symplify\ComposerJsonManipulator\DependencyInjection\Extension;

use ECSPrefix20210825\Symfony\Component\Config\FileLocator;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210825\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class ComposerJsonManipulatorExtension extends \ECSPrefix20210825\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function load($configs, $containerBuilder) : void
    {
        $phpFileLoader = new \ECSPrefix20210825\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210825\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
