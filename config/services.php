<?php

declare(strict_types=1);

use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use Symplify\PhpConfigPrinter\Naming\ClassNaming;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\Finder\SmartFinder;
use Symplify\SmartFileSystem\SmartFileSystem;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->import(__DIR__ . '/services/services_cache.php');

    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire()
        ->autoconfigure();

    $services->load('Symplify\EasyCodingStandard\\', __DIR__ . '/../src')
        ->exclude([
            // onyl for "bin/ecs" file, where containre does not exist yet
            __DIR__ . '/../src/Bundle',
            __DIR__ . '/../src/Bootstrap',
            __DIR__ . '/../src/DependencyInjection',
            __DIR__ . '/../src/HttpKernel',
            __DIR__ . '/../src/Exception',
            __DIR__ . '/../src/ValueObject',
        ]);

    $services->set(Terminal::class);

    $services->set(FileSystemGuard::class);
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemFilter::class);

    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)
        ->factory([service(SymfonyStyleFactory::class), 'create']);

    $services->set(EasyCodingStandardStyle::class)
        ->factory([service(EasyCodingStandardStyleFactory::class), 'create']);

    $services->set(WhitespacesFixerConfig::class)
        ->factory([service(WhitespacesFixerConfigFactory::class), 'create']);

    $services->set(NoCheckersLoaderReporter::class);
    $services->set(ClassNaming::class);
};
