<?php

namespace ECSPrefix20210508;

use ECSPrefix20210508\Psr\Cache\CacheItemPoolInterface;
use ECSPrefix20210508\Psr\SimpleCache\CacheInterface;
use ECSPrefix20210508\Symfony\Component\Cache\Adapter\FilesystemAdapter;
use ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapter;
use ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use ECSPrefix20210508\Symfony\Component\Cache\Psr16Cache;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\ECSPrefix20210508\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->set(\ECSPrefix20210508\Symfony\Component\Cache\Psr16Cache::class);
    $services->alias(\ECSPrefix20210508\Psr\SimpleCache\CacheInterface::class, \ECSPrefix20210508\Symfony\Component\Cache\Psr16Cache::class);
    $services->set(\ECSPrefix20210508\Symfony\Component\Cache\Adapter\FilesystemAdapter::class)->args(['$namespace' => '%cache_namespace%', '$defaultLifetime' => 0, '$directory' => '%cache_directory%']);
    $services->alias(\ECSPrefix20210508\Psr\Cache\CacheItemPoolInterface::class, \ECSPrefix20210508\Symfony\Component\Cache\Adapter\FilesystemAdapter::class);
    $services->alias(\ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapterInterface::class, \ECSPrefix20210508\Symfony\Component\Cache\Adapter\TagAwareAdapter::class);
};
