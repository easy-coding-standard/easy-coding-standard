<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection\Extension;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symplify\EasyCodingStandard\Yaml\FileLoader\CheckerTolerantYamlFileLoader;

final class EasyCodingStandardExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $containerBuilder): void
    {
        // needed for parameter shifting of sniff/fixer params
        $checkerTolerantYamlFileLoader = new CheckerTolerantYamlFileLoader($containerBuilder, new FileLocator(
            __DIR__ . '/../../../config'
        ));

        $checkerTolerantYamlFileLoader->load('config.yaml');
    }
}
