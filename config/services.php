<?php

declare (strict_types=1);
namespace ECSPrefix20220220;

use PHP_CodeSniffer\Fixer;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220220\Symfony\Component\Console\Terminal;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
use ECSPrefix20220220\Symplify\PackageBuilder\Yaml\ParametersMerger;
use ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemFilter;
use ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20220220\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20220220\Symplify\SmartFileSystem\Finder\SmartFinder;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem;
use function ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\service;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../src')->exclude([
        // only for "bin/ecs" file, where container does not exist yet
        __DIR__ . '/../src/DependencyInjection',
        __DIR__ . '/../src/Kernel',
        __DIR__ . '/../src/Exception',
        __DIR__ . '/../src/ValueObject',
        // for 3rd party tests
        __DIR__ . '/../src/Testing',
    ]);
    $services->set(\ECSPrefix20220220\Symfony\Component\Console\Terminal::class);
    $services->set(\ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemGuard::class);
    $services->set(\ECSPrefix20220220\Symplify\SmartFileSystem\Finder\FinderSanitizer::class);
    $services->set(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileSystem::class);
    $services->set(\ECSPrefix20220220\Symplify\SmartFileSystem\Finder\SmartFinder::class);
    $services->set(\ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemFilter::class);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class);
    $services->set(\ECSPrefix20220220\Symfony\Component\Console\Style\SymfonyStyle::class)->factory([\ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\service(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory::class), 'create']);
    $services->set(\ECSPrefix20220220\Symplify\PackageBuilder\Yaml\ParametersMerger::class);
    $services->set(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle::class)->factory([\ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory::class), 'create']);
    $services->set(\PhpCsFixer\WhitespacesFixerConfig::class)->factory([\ECSPrefix20220220\Symfony\Component\DependencyInjection\Loader\Configurator\service(\Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory::class), 'create']);
    // code sniffer
    $services->set(\PHP_CodeSniffer\Fixer::class);
    // fixer
    $services->set(\PhpCsFixer\Differ\UnifiedDiffer::class);
    $services->alias(\PhpCsFixer\Differ\DifferInterface::class, \PhpCsFixer\Differ\UnifiedDiffer::class);
    $services->set(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
};
