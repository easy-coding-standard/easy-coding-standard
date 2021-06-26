<?php

declare (strict_types=1);
namespace ECSPrefix20210626\Symplify\ComposerJsonManipulator\DependencyInjection\Extension;

use ECSPrefix20210626\Symfony\Component\Config\FileLocator;
use ECSPrefix20210626\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210626\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210626\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class ComposerJsonManipulatorExtension extends \ECSPrefix20210626\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @return void
     */
    public function load(array $configs, \ECSPrefix20210626\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $phpFileLoader = new \ECSPrefix20210626\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210626\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
