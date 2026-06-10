<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix202606\Entropy\Container\Container;
use PHP_CodeSniffer\Util\Tokens;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Differ\UnifiedDiffer;
use PhpCsFixer\WhitespacesFixerConfig;
use ECSPrefix202606\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Caching\Cache;
use Symplify\EasyCodingStandard\Caching\CacheFactory;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Console\Output\CheckstyleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\GitlabOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Output\JUnitOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\Console\Style\SymfonyStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use ECSPrefix202606\Webmozart\Assert\Assert;
final class LazyContainerFactory
{
    /**
     * Output formatters are registered explicitly, so they can be collected by contract.
     *
     * @var array<class-string>
     */
    private const OUTPUT_FORMATTER_CLASSES = [GitlabOutputFormatter::class, CheckstyleOutputFormatter::class, ConsoleOutputFormatter::class, JsonOutputFormatter::class, JUnitOutputFormatter::class];
    /**
     * @param string[] $configFiles
     */
    public function create(array $configFiles = []): ECSConfig
    {
        $this->loadPHPCodeSnifferConstants();
        $ecsConfig = new ECSConfig();
        // console
        $ecsConfig->service(EasyCodingStandardStyle::class, static function (Container $container): EasyCodingStandardStyle {
            /** @var EasyCodingStandardStyleFactory $easyCodingStandardStyleFactory */
            $easyCodingStandardStyleFactory = $container->make(EasyCodingStandardStyleFactory::class);
            return $easyCodingStandardStyleFactory->create();
        });
        $ecsConfig->service(SymfonyStyle::class, static function (): SymfonyStyle {
            return SymfonyStyleFactory::create();
        });
        // whitespace
        $ecsConfig->service(WhitespacesFixerConfig::class, static function (): WhitespacesFixerConfig {
            $whitespacesFixerConfigFactory = new WhitespacesFixerConfigFactory();
            return $whitespacesFixerConfigFactory->create();
        });
        // caching
        $ecsConfig->service(Cache::class, static function (Container $container): Cache {
            /** @var CacheFactory $cacheFactory */
            $cacheFactory = $container->make(CacheFactory::class);
            return $cacheFactory->create();
        });
        // diffing
        $ecsConfig->service(DifferInterface::class, static function (): DifferInterface {
            return new UnifiedDiffer();
        });
        // output formatters - autowired eagerly so OutputFormatterCollector can find them by contract
        foreach (self::OUTPUT_FORMATTER_CLASSES as $outputFormatterClass) {
            $ecsConfig->make($outputFormatterClass);
        }
        // load default config first
        $configFiles = array_merge([__DIR__ . '/../../config/config.php'], $configFiles);
        foreach ($configFiles as $configFile) {
            $configClosure = require $configFile;
            Assert::isCallable($configClosure);
            $configClosure($ecsConfig);
        }
        return $ecsConfig;
    }
    /**
     * These are require for PHP_CodeSniffer to run
     */
    private function loadPHPCodeSnifferConstants(): void
    {
        if (!defined('PHP_CODESNIFFER_VERBOSITY')) {
            // initialize token with INT type, otherwise php-cs-fixer and php-parser breaks
            if (!defined('T_MATCH')) {
                define('T_MATCH', 5000);
            }
            if (!defined('T_READONLY')) {
                define('T_READONLY', 5010);
            }
            if (!defined('T_ENUM')) {
                define('T_ENUM', 5015);
            }
            if (!defined('T_NULLSAFE_OBJECT_OPERATOR')) {
                define('T_NULLSAFE_OBJECT_OPERATOR', 5020);
            }
            // for PHP_CodeSniffer
            define('PHP_CODESNIFFER_CBF', \false);
            define('PHP_CODESNIFFER_VERBOSITY', 0);
            new Tokens();
        }
    }
}
