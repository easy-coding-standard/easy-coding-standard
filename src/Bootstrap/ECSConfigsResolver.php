<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Bootstrap;

use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Set\ConstantReflectionSetFactory;
use Symplify\EasyCodingStandard\Set\EasyCodingStandardSetProvider;
use Symplify\SetConfigResolver\SetAwareConfigResolver;
use Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs;

/**
 * @deprecated Move to direct $containerConfigurator->import() approach, instead of our hidden nested magic with same result
 */
final class ECSConfigsResolver
{
    /**
     * @var SetAwareConfigResolver
     */
    private $setAwareConfigResolver;

    public function __construct()
    {
        $easyCodingStandardSetProvider = new EasyCodingStandardSetProvider(new ConstantReflectionSetFactory());
        $this->setAwareConfigResolver = new SetAwareConfigResolver($easyCodingStandardSetProvider);
    }

    public function resolveFromInput(InputInterface $input): BootstrapConfigs
    {
        $configFileInfos = [];

        $mainConfigFileInfo = $this->setAwareConfigResolver->resolveFromInputWithFallback($input, ['ecs.php']);

        if ($mainConfigFileInfo !== null) {
            // 2. "parameters > set" in provided yaml files
            $parameterSetsConfigs = $this->setAwareConfigResolver->resolveFromParameterSetsFromConfigFiles(
                [$mainConfigFileInfo]
            );

            $configFileInfos = array_merge($configFileInfos, $parameterSetsConfigs);
        }

        return new BootstrapConfigs($mainConfigFileInfo, $configFileInfos);
    }
}
