<?php

declare (strict_types=1);
namespace ECSPrefix202206;

use PHP_CodeSniffer\Fixer;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix202206\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202206\Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Caching\Cache;
use Symplify\EasyCodingStandard\Caching\CacheFactory;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202206\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix202206\Symplify\PackageBuilder\Yaml\ParametersMerger;
use ECSPrefix202206\Symplify\SmartFileSystem\FileSystemFilter;
use ECSPrefix202206\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix202206\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix202206\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix202206\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (ECSConfig $ecsConfig) : void {
    $parameters = $ecsConfig->parameters();
    $ecsConfig->indentation(Option::INDENTATION_SPACES);
    $ecsConfig->lineEnding(\PHP_EOL);
    $cacheDirectory = \sys_get_temp_dir() . '/changed_files_detector%env(TEST_SUFFIX)%';
    if (StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
        $cacheDirectory .= '_' . StaticVersionResolver::PACKAGE_VERSION;
    }
    // @todo turn these into methods :)
    $parameters->set(Option::CACHE_DIRECTORY, $cacheDirectory);
    $cacheNamespace = \str_replace(\DIRECTORY_SEPARATOR, '_', \getcwd());
    $parameters->set(Option::CACHE_NAMESPACE, $cacheNamespace);
    // parallel
    $ecsConfig->parallel();
    // how many files are processed in single process
    $parameters->set(Option::PARALLEL_JOB_SIZE, 60);
    $parameters->set(Option::PARALLEL_MAX_NUMBER_OF_PROCESSES, 16);
    $parameters->set(Option::PARALLEL_TIMEOUT_IN_SECONDS, 120);
    $ecsConfig->paths([]);
    $parameters->set(Option::FILE_EXTENSIONS, ['php']);
    $parameters->set('env(TEST_SUFFIX)', '');
    $services = $ecsConfig->services();
    $services->defaults()->public()->autowire();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../src')->exclude([
        // only for "bin/ecs" file, where container does not exist yet
        __DIR__ . '/../src/Config/ECSConfig.php',
        __DIR__ . '/../src/DependencyInjection',
        __DIR__ . '/../src/Kernel',
        __DIR__ . '/../src/Exception',
        __DIR__ . '/../src/ValueObject',
        // for 3rd party tests
        __DIR__ . '/../src/Testing',
    ]);
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/*/ValueObject/*']);
    $services->set(Cache::class)->factory([service(CacheFactory::class), 'create']);
    $services->set(Terminal::class);
    $services->set(FileSystemGuard::class);
    $services->set(FinderSanitizer::class);
    $services->set(SmartFileSystem::class);
    $services->set(SmartFinder::class);
    $services->set(FileSystemFilter::class);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
    $services->set(ParametersMerger::class);
    $services->set(EasyCodingStandardStyle::class)->factory([service(EasyCodingStandardStyleFactory::class), 'create']);
    $services->set(WhitespacesFixerConfig::class)->factory([service(WhitespacesFixerConfigFactory::class), 'create']);
    // code sniffer
    $services->set(Fixer::class);
    // fixer
    $services->set(UnifiedDiffer::class);
    $services->alias(DifferInterface::class, UnifiedDiffer::class);
    $services->set(FixerFileProcessor::class);
};
