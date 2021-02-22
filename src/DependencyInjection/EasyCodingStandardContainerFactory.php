<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\DependencyInjection;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs;

final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(InputInterface $input): ContainerInterface
    {
        $ecsConfigsResolver = new ECSConfigsResolver();
        $bootstrapConfigs = $ecsConfigsResolver->resolveFromInput($input);

        return $this->createFromFromBootstrapConfigs($bootstrapConfigs);
    }

    public function createFromFromBootstrapConfigs(BootstrapConfigs $bootstrapConfigs): ContainerInterface
    {
        $environment = 'prod' . random_int(1, 100000);
        $easyCodingStandardKernel = new EasyCodingStandardKernel($environment, StaticInputDetector::isDebug());

        $configFileInfos = $bootstrapConfigs->getConfigFileInfos();
        if ($configFileInfos !== []) {
            $easyCodingStandardKernel->setConfigs($configFileInfos);
        }

        $easyCodingStandardKernel->boot();

        $container = $easyCodingStandardKernel->getContainer();

        if ($configFileInfos !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($configFileInfos);
        }

        return $container;
    }
}
