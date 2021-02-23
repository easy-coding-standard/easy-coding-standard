<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    # A. monorepo
    $containerConfigurator->import(__DIR__ . '/../../../coding-standard/config/symplify.php', null, 'not_found');

    # B. installed as dependency
    $containerConfigurator->import(
        __DIR__ . '/../../vendor/symplify/coding-standard/config/symplify.php',
        null,
        'not_found'
    );
};
