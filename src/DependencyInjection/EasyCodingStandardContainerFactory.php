<?php

namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs;
final class EasyCodingStandardContainerFactory
{
    /**
     * @param \ECSPrefix20210507\Symfony\Component\Console\Input\InputInterface $input
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function createFromFromInput($input)
    {
        $ecsConfigsResolver = new ECSConfigsResolver();
        $bootstrapConfigs = $ecsConfigsResolver->resolveFromInput($input);
        return $this->createFromFromBootstrapConfigs($bootstrapConfigs);
    }
    /**
     * @param \Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs $bootstrapConfigs
     * @return \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function createFromFromBootstrapConfigs($bootstrapConfigs)
    {
        $environment = 'prod' . \random_int(1, 100000);
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
