<?php

declare (strict_types=1);
namespace ECSPrefix202212;

use ECSPrefix202212\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use ECSPrefix202212\Symplify\SmartFileSystem\SmartFileSystem;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->set(SmartFileSystem::class);
};
