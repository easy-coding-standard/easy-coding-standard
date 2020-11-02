<?php

declare(strict_types=1);

use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symplify\PackageBuilder\Functions\service_polyfill;

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
            '$itemsPool' => service_polyfill(Psr16Adapter::class),
            '$tagsPool' => service_polyfill(Psr16Adapter::class),
        ]);
};
