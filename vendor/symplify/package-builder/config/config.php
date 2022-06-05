<?php

declare (strict_types=1);
namespace ECSPrefix20220605;

use ECSPrefix20220605\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220605\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix20220605\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix20220605\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix20220605\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(\ECSPrefix20220605\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter::class);
    $services->set(\ECSPrefix20220605\Symplify\PackageBuilder\Console\Output\ConsoleDiffer::class);
    $services->set(\ECSPrefix20220605\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(\ECSPrefix20220605\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20220605\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
