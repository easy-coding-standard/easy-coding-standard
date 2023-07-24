<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Illuminate\Container\Container;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\WhitespacesFixerConfig;
use Symplify\EasyCodingStandard\Config\ECSConfig;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyleFactory;
use Symplify\EasyCodingStandard\FixerRunner\WhitespacesFixerConfigFactory;
use Webmozart\Assert\Assert;

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

        // whitespace
        $ecsContainer->singleton(WhitespacesFixerConfig::class, function () {
            $whitespacesFixerConfigFactory = new WhitespacesFixerConfigFactory();
            return $whitespacesFixerConfigFactory->create();
        });


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
