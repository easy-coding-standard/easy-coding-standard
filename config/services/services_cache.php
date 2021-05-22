<?php

declare (strict_types=1);
namespace ECSPrefix20210522;

use ECSPrefix20210522\Nette\Caching\Cache;
use ECSPrefix20210522\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Caching\NetteCacheFactory;
use function ECSPrefix20210522\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\ECSPrefix20210522\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->set(\ECSPrefix20210522\Nette\Caching\Cache::class)->factory([\ECSPrefix20210522\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\Caching\NetteCacheFactory::class), 'create']);
};
