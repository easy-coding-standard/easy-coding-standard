<?php

declare (strict_types=1);
namespace ECSPrefix20210608;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Caching\Cache;
use Symplify\EasyCodingStandard\Caching\CacheFactory;
use function ECSPrefix20210608\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../packages')->exclude(['*/Exception/*', '*/ValueObject/*', __DIR__ . '/../packages/SniffRunner/ValueObject/File.php', __DIR__ . '/../packages/Caching/ValueObject/', __DIR__ . '/../packages/Caching/Cache.php']);
    $services->set(\Symplify\EasyCodingStandard\Caching\Cache::class)->factory([\ECSPrefix20210608\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\Caching\CacheFactory::class), 'create']);
};
