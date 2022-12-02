<?php

declare (strict_types=1);
namespace ECSPrefix202212;

use ECSPrefix202212\SebastianBergmann\Diff\Differ;
use ECSPrefix202212\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202212\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix202212\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use ECSPrefix202212\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use ECSPrefix202212\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};
