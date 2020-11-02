<?php

declare(strict_types=1);

use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Compiler\ValueObject\Option;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PackageBuilder\Process\ProcessRunner;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\ref;

return static function (ContainerConfigurator $containerConfigurator): void {
    $parameters = $containerConfigurator->parameters();

    $parameters->set(Option::DATA_DIR, '%kernel.project_dir%/build');
    $parameters->set(Option::BUILD_DIR, '%kernel.project_dir%/..');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCodingStandard\Compiler\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/HttpKernel', __DIR__ . '/../src/ValueObject']);

    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)
        ->factory([ref(SymfonyStyleFactory::class), 'create']);

    $services->set(SmartFileSystem::class);

    $services->set(ProcessRunner::class);
};
