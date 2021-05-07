<?php

namespace ECSPrefix20210507;

use ECSPrefix20210507\Symfony\Component\Cache\Adapter\Psr16Adapter;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\TagAwareAdapter;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator->services();
    $services->defaults()->autowire()->autoconfigure()->public();
    $services->load('Symplify\\EasyCodingStandard\\ChangedFilesDetector\\', __DIR__ . '/../src');
    $services->set(Psr16Adapter::class);
    $services->set(TagAwareAdapter::class)->args(['$itemsPool' => service(Psr16Adapter::class), '$tagsPool' => service(Psr16Adapter::class)]);
};
