<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->public();

    $services->load('Symplify\EasyCodingStandard\ChangedFilesDetector\\', __DIR__ . '/../src');

    $services->set(Psr16Adapter::class);

    $services->set(TagAwareAdapter::class)
        ->args([
            '$itemsPool' => ref(Psr16Adapter::class),
            '$tagsPool' => ref(Psr16Adapter::class),
        ]);
};
