<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCodingStandard\ChangedFilesDetector\\', __DIR__ . '/../src');

    $services->set(Psr16Adapter::class);

    $services->set(TagAwareAdapter::class)
        ->args([
            '$itemsPool' => ref(Psr16Adapter::class),
            '$tagsPool' => ref(Psr16Adapter::class),
        ]);
};
