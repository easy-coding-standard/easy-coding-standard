<?php

namespace Symplify\ConsoleColorDiff\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

final class ConsoleColorDiffExtension extends Extension
{
    /**
     * @param string[] $configs
     * @return void
     */
    public function load(array $configs, ContainerBuilder $containerBuilder)
    {
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../config'));

        $phpFileLoader->load('config.php');
    }
}
