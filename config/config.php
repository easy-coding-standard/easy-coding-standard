<?php

declare (strict_types=1);
namespace ECSPrefix202306;

use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\AssignmentInConditionSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\CodeAnalysis\UnusedFunctionParameterSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Classes\PropertyDeclarationSniff;
use PHP_CodeSniffer\Standards\PSR2\Sniffs\Methods\MethodDeclarationSniff;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP\CommentedOutCodeSniff;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix202306\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202306\Symfony\Component\Console\Terminal;
use ECSPrefix202306\Symfony\Component\Filesystem\Filesystem;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Caching\Cache;
use Symplify\EasyCodingStandard\Caching\CacheFactory;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\OutputFormatterCollector;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\Console\Style\SymfonyStyleFactory;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\DependencyInjection\SimpleParameterProvider;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202306\Symplify\PackageBuilder\Yaml\ParametersMerger;
use function ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function ECSPrefix202306\Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->indentation(Option::INDENTATION_SPACES);
    $ecsConfig->lineEnding(\PHP_EOL);
    $cacheDirectory = \sys_get_temp_dir() . '/changed_files_detector';
    if (StaticVersionResolver::PACKAGE_VERSION !== '@package_version@') {
        $cacheDirectory .= '_' . StaticVersionResolver::PACKAGE_VERSION;
    }
    $ecsConfig->cacheDirectory($cacheDirectory);
    $cacheNamespace = \str_replace(\DIRECTORY_SEPARATOR, '_', \getcwd());
    $ecsConfig->cacheNamespace($cacheNamespace);
    $ecsConfig->parallel();
    // ECS only knows about errors, these are the classes allowed to promote warnings to errors
    $ecsConfig->reportSniffClassWarnings([AssignmentInConditionSniff::class, PropertyDeclarationSniff::class, MethodDeclarationSniff::class, CommentedOutCodeSniff::class, UnusedFunctionParameterSniff::class]);
    $ecsConfig->paths([]);
    $ecsConfig->skip([]);
    $ecsConfig->fileExtensions(['php']);
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
    // output formatters
    $services->set(ConsoleOutputFormatter::class)->tag(OutputFormatterInterface::class);
    $services->set(JsonOutputFormatter::class)->tag(OutputFormatterInterface::class);
    $services->set(OutputFormatterCollector::class)->arg('$outputFormatters', tagged_iterator(OutputFormatterInterface::class));
    $services->load('Symplify\\EasyCodingStandard\\', __DIR__ . '/../packages')->exclude([__DIR__ . '/../packages/*/ValueObject/*']);
    $services->set(Filesystem::class);
    $services->set(Cache::class)->factory([service(CacheFactory::class), 'create']);
    $services->set(Terminal::class);
    $services->set(SymfonyStyleFactory::class);
    $services->set(SymfonyStyle::class)->factory([service(SymfonyStyleFactory::class), 'create']);
    $services->set(ParametersMerger::class);
    $services->set(SimpleParameterProvider::class);
    $services->set(EasyCodingStandardStyle::class)->factory([service(EasyCodingStandardStyleFactory::class), 'create']);
    $services->set(WhitespacesFixerConfig::class)->factory([service(WhitespacesFixerConfigFactory::class), 'create']);
    // php code sniffer
    $services->set(Fixer::class);
    $services->set(SniffFileProcessor::class)->arg('$sniffs', tagged_iterator(Sniff::class));
    // php-cs-fixer
    $services->set(FixerFileProcessor::class)->arg('$fixers', tagged_iterator(FixerInterface::class));
    $services->set(UnifiedDiffer::class);
    $services->alias(DifferInterface::class, UnifiedDiffer::class);
};
