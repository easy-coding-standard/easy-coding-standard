<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\DependencyInjection\Extension;

use ECSPrefix20210706\Symfony\Component\Config\FileLocator;
use ECSPrefix20210706\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210706\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210706\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SymplifyCodingStandardExtension extends \ECSPrefix20210706\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @return void
     */
    public function load(array $configs, \ECSPrefix20210706\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        // needed for parameter shifting of sniff/fixer params
        $phpFileLoader = new \ECSPrefix20210706\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210706\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('config.php');
    }
}
