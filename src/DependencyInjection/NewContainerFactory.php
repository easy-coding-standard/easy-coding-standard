<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Illuminate\Container\Container;
use Illuminate\Container\RewindableGenerator;
use PHP_CodeSniffer\Fixer;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
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
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\ConflictingCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveExcludedCheckersCompilerPass;
use Symplify\EasyCodingStandard\DependencyInjection\CompilerPass\RemoveMutualCheckersCompilerPass;
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Webmozart\Assert\Assert;

final class NewContainerFactory
{
    /**
     * @param string[] $configFiles
     */
    public function create(array $configFiles = []): Container
    {
        $ecsContainer = null;
        $this->loadPHPCodeSnifferConstants();

        $ecsConfig = new ECSConfig();

        // console
        $ecsConfig->singleton(EasyCodingStandardStyle::class, static function (Container $container) {
            $easyCodingStandardStyleFactory = $container->make(EasyCodingStandardStyleFactory::class);
            return $easyCodingStandardStyleFactory->create();
        });

        $ecsConfig->singleton(SymfonyStyle::class, static function (Container $container) {
            $symfonyStyleFactory = $container->make(SymfonyStyleFactory::class);
            return $symfonyStyleFactory->create();
        });

        $ecsConfig->singleton(Fixer::class);

        // whitespace
        $ecsConfig->singleton(WhitespacesFixerConfig::class, static function (): WhitespacesFixerConfig {
            $whitespacesFixerConfigFactory = new WhitespacesFixerConfigFactory();
            return $whitespacesFixerConfigFactory->create();
        });

        // caching
        $ecsConfig->singleton(Cache::class, static function (Container $container) {
            $cacheFactory = $container->make(CacheFactory::class);
            return $cacheFactory->create();
        });

        // output
        $ecsConfig->singleton(ConsoleOutputFormatter::class);
        $ecsConfig->tag(ConsoleOutputFormatter::class, OutputFormatterInterface::class);

        $ecsConfig->singleton(JsonOutputFormatter::class);
        $ecsConfig->tag(JsonOutputFormatter::class, OutputFormatterInterface::class);

        $ecsConfig->singleton(
            OutputFormatterCollector::class,
            static function (Container $container): OutputFormatterCollector {
                /** @var RewindableGenerator<int, OutputFormatterInterface> $outputFormattersRewindableGenerator */
                $outputFormattersRewindableGenerator = $container->tagged(OutputFormatterInterface::class);
                return new OutputFormatterCollector(iterator_to_array(
                    $outputFormattersRewindableGenerator->getIterator()
                ));
            }
        );

        $ecsConfig->singleton(DifferInterface::class, static fn (): DifferInterface => new UnifiedDiffer());

        $ecsConfig->singleton(FixerFileProcessor::class, function (Container $container): FixerFileProcessor {
            $fixers = $this->getTaggedServicesArray($container, FixerInterface::class);

            return new FixerFileProcessor(
                $container->make(FileToTokensParser::class),
                $container->make(Skipper::class),
                $container->make(DifferInterface::class),
                $container->make(EasyCodingStandardStyle::class),
                $container->make(Filesystem::class),
                $container->make(FileDiffFactory::class),
                $fixers
            );
        });

        $ecsConfig->singleton(SniffFileProcessor::class, function (Container $container): SniffFileProcessor {
            $sniffs = $this->getTaggedServicesArray($container, Sniff::class);

            return new SniffFileProcessor(
                $container->make(Fixer::class),
                $container->make(FileFactory::class),
                $container->make(DifferInterface::class),
                $container->make(SniffMetadataCollector::class),
                $container->make(Filesystem::class),
                $container->make(FileDiffFactory::class),
                $sniffs,
            );
        });

        // load default config first
        $configFiles = array_merge([__DIR__ . '/../../config/config.php'], $configFiles);

        foreach ($configFiles as $configFile) {
            $configClosure = require $configFile;
            Assert::isCallable($configClosure);

            $configClosure($ecsConfig);
        }

        // compiler passes-like
        $ecsConfig->beforeResolving(
            FixerFileProcessor::class,
            static function ($object, $misc, ECSConfig $ecsConfig): void {
                $removeExcludedCheckersCompilerPass = new RemoveExcludedCheckersCompilerPass();
                $removeExcludedCheckersCompilerPass->process($ecsConfig);
            }
        );

        $hasRunAfterResolving = false;

        $ecsConfig->afterResolving(static function ($object, ECSConfig $ecsConfig) use (&$hasRunAfterResolving): void {
            // run just once
            if ($hasRunAfterResolving) {
                return;
            }

            $removeMutualCheckersCompilerPass = new RemoveMutualCheckersCompilerPass();
            $removeMutualCheckersCompilerPass->process($ecsConfig);

            $conflictingCheckersCompilerPass = new ConflictingCheckersCompilerPass();
            $conflictingCheckersCompilerPass->process($ecsConfig);

            $hasRunAfterResolving = true;
        });

        return $ecsConfig;
    }

    /**
     * These are require for PHP_CodeSniffer to run
     */
    private function loadPHPCodeSnifferConstants(): void
    {
        if (! defined('PHP_CODESNIFFER_VERBOSITY')) {
            // initalize token with INT type, otherwise php-cs-fixer and php-parser breaks
            if (! defined('T_MATCH')) {
                define('T_MATCH', 5000);
            }

            if (! defined('T_READONLY')) {
                define('T_READONLY', 5010);
            }

            if (! defined('T_ENUM')) {
                define('T_ENUM', 5015);
            }

            // for PHP_CodeSniffer
            define('PHP_CODESNIFFER_CBF', false);
            define('PHP_CODESNIFFER_VERBOSITY', 0);

            new Tokens();
        }
    }

    /**
     * @template TType of object
     * @param class-string<TType> $type
     * @return TType[]
     */
    private function getTaggedServicesArray(Container $container, string $type): array
    {
        /** @var RewindableGenerator<TType>|TType[] $rewindableGenerator */
        $rewindableGenerator = $container->tagged($type);

        // turn generator to array
        if (! is_array($rewindableGenerator)) {
            return iterator_to_array($rewindableGenerator->getIterator());
        }

        // return array
        return $rewindableGenerator;
    }
}
