<?php

declare (strict_types=1);
namespace ECSPrefix20220221;

use ECSPrefix20220221\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220221\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix20220221\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix20220221\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix20220221\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->set(\ECSPrefix20220221\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter::class);
    $services->set(\ECSPrefix20220221\Symplify\PackageBuilder\Console\Output\ConsoleDiffer::class);
    $services->set(\ECSPrefix20220221\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(\ECSPrefix20220221\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20220221\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
