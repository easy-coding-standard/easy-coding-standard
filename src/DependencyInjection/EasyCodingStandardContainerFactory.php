<?php

namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210508\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210508\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs;
final class EasyCodingStandardContainerFactory
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function createFromFromInput(\ECSPrefix20210508\Symfony\Component\Console\Input\InputInterface $input)
    {
        $ecsConfigsResolver = new \Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver();
        $bootstrapConfigs = $ecsConfigsResolver->resolveFromInput($input);
        return $this->createFromFromBootstrapConfigs($bootstrapConfigs);
    }
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    public function createFromFromBootstrapConfigs(\Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs $bootstrapConfigs)
    {
        $environment = 'prod' . \random_int(1, 100000);
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel($environment, \Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
        $configFileInfos = $bootstrapConfigs->getConfigFileInfos();
        if ($configFileInfos !== []) {
            $easyCodingStandardKernel->setConfigs($configFileInfos);
        }
        $easyCodingStandardKernel->boot();
        $container = $easyCodingStandardKernel->getContainer();
        if ($configFileInfos !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(\Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($configFileInfos);
        }
        return $container;
    }
}
