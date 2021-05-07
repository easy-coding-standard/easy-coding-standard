<?php

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use ECSPrefix20210507\Symfony\Component\Config\FileLocator;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class EasyCodingStandardExtension extends Extension
{
    /**
     * @param string[] $configs
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function load(array $configs, $containerBuilder)
    {
        // needed for parameter shifting of sniff/fixer params
        $phpFileLoader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
