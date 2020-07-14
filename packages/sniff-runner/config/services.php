<?php declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use PHP_CodeSniffer\Fixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCodingStandard\SniffRunner\\', __DIR__ . '/../src')
        ->exclude([
            __DIR__ . '/../src/Exception/*',
            __DIR__ . '/../src/ValueObject/*',
        ]);

    $services->set(Fixer::class);
};
