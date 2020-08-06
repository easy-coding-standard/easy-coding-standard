<?php declare(strict_types=1);

use PHP_CodeSniffer\Fixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\EasyCodingStandard\SniffRunner\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Exception/*', __DIR__ . '/../src/ValueObject/*']);

    $services->set(Fixer::class);
};
