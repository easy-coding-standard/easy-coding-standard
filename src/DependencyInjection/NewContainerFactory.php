<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Illuminate\Container\Container;
use Illuminate\Container\RewindableGenerator;
use PHP_CodeSniffer\Fixer;
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
use Symplify\EasyCodingStandard\Error\FileDiffFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\FixerRunner\Parser\FileToTokensParser;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\DataCollector\SniffMetadataCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use Webmozart\Assert\Assert;
use PHP_CodeSniffer\Sniffs\Sniff;

/**
 * @api will be used in new DI
 */
final class NewContainerFactory
{
    /**
     * @param string[] $configFiles
     */
    public function create(array $configFiles = []): Container
    {
        $ecsContainer = new ECSConfig();

        // console
        $ecsContainer->singleton(EasyCodingStandardStyle::class, static function (Container $container) {
            $easyCodingStandardStyleFactory = $container->make(EasyCodingStandardStyleFactory::class);
            return $easyCodingStandardStyleFactory->create();
        });

        $ecsContainer->singleton(SymfonyStyle::class, static function (Container $container) {
            $symfonyStyleFactory = $container->make(SymfonyStyleFactory::class);
            return $symfonyStyleFactory->create();
        });


        // whitespace
        $ecsContainer->singleton(WhitespacesFixerConfig::class, function () {
            $whitespacesFixerConfigFactory = new WhitespacesFixerConfigFactory();
            return $whitespacesFixerConfigFactory->create();
        });

        // caching
        $ecsContainer->singleton(Cache::class, function (Container $container) {
            $cacheFactory = $container->make(CacheFactory::class);
            return $cacheFactory->create();
        });

        // output
        $ecsContainer->singleton(ConsoleOutputFormatter::class);
        $ecsContainer->tag(ConsoleOutputFormatter::class, OutputFormatterInterface::class);

        $ecsContainer->singleton(JsonOutputFormatter::class);
        $ecsContainer->tag(JsonOutputFormatter::class, OutputFormatterInterface::class);

        $ecsContainer->singleton(OutputFormatterCollector::class, function (Container $container) {
            /** @var RewindableGenerator<int, OutputFormatterInterface> $outputFormattersRewindableGenerator */
            $outputFormattersRewindableGenerator = $container->tagged(OutputFormatterInterface::class);
            return new OutputFormatterCollector(iterator_to_array($outputFormattersRewindableGenerator->getIterator()));
        });

        $ecsContainer->singleton(DifferInterface::class, function (): DifferInterface {
            return new UnifiedDiffer();
        });

        $ecsContainer->singleton(FixerFileProcessor::class, function (Container $container) {
            /** @var RewindableGenerator<int, FixerInterface> $fixerRewindableGenerator */
            $fixerRewindableGenerator = $container->tagged(FixerInterface::class);

            return new FixerFileProcessor(
                $container->make(FileToTokensParser::class),
                $container->make(Skipper::class),
                $container->make(DifferInterface::class),
                $container->make(EasyCodingStandardStyle::class),
                $container->make(Filesystem::class),
                $container->make(FileDiffFactory::class),
                iterator_to_array($fixerRewindableGenerator->getIterator())
            );
        });

        $ecsContainer->singleton(SniffFileProcessor::class, function (Container $container) {
            /** @var RewindableGenerator<Sniff> $sniffRewindableGenerator */
            $sniffRewindableGenerator = $container->tagged(Sniff::class);

            return new SniffFileProcessor(
                $container->make(Fixer::class),
                $container->make(FileFactory::class),
                $container->make(DifferInterface::class),
                $container->make(SniffMetadataCollector::class),
                $container->make(Filesystem::class),
                $container->make(FileDiffFactory::class),
                $container->make(PrivatesAccessor::class),
                iterator_to_array($sniffRewindableGenerator->getIterator())
            );
        });
//
//    // php-cs-fixer
//    $services->set(FixerFileProcessor::class)
//        ->arg('$fixers', tagged_iterator(FixerInterface::class));


        // load default config first
        $configFiles = array_merge([__DIR__ . '/../../config/config.php'], $configFiles);

        foreach ($configFiles as $configFile) {
            $configClosure = require $configFile;
            Assert::isCallable($configClosure);

            $configClosure($ecsContainer);
        }

        return $ecsContainer;
    }
}
