<?php

declare (strict_types=1);
namespace ECSPrefix202301;

use ECSPrefix202301\SebastianBergmann\Diff\Differ;
use ECSPrefix202301\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202301\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix202301\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix202301\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix202301\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};
