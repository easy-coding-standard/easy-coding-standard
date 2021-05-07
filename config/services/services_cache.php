<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Psr\Cache\CacheItemPoolInterface;
use ECSPrefix20210507\Psr\SimpleCache\CacheInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\FilesystemAdapter;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\TagAwareAdapter;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Psr16Cache;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->set(Psr16Cache::class);
    $services->alias(CacheInterface::class, Psr16Cache::class);
    $services->set(FilesystemAdapter::class)->args(['$namespace' => '%cache_namespace%', '$defaultLifetime' => 0, '$directory' => '%cache_directory%']);
    $services->alias(CacheItemPoolInterface::class, FilesystemAdapter::class);
    $services->alias(TagAwareAdapterInterface::class, TagAwareAdapter::class);
};
