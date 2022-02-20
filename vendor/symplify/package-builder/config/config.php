<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use ECSPrefix20220220\SebastianBergmann\Diff\Differ;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix20220220\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter::class);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Output\ConsoleDiffer::class);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(\ECSPrefix20220220\SebastianBergmann\Diff\Differ::class);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Reflection\PrivatesAccessor::class);
};
