<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210517\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use ECSPrefix20210517\Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(\ECSPrefix20210517\Symfony\Component\Console\Input\InputInterface $input) : \ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $ecsConfigsResolver = new \Symplify\EasyCodingStandard\Bootstrap\ECSConfigsResolver();
        $bootstrapConfigs = $ecsConfigsResolver->resolveFromInput($input);
        return $this->createFromFromBootstrapConfigs($bootstrapConfigs);
    }
    public function createFromFromBootstrapConfigs(\ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject\Bootstrap\BootstrapConfigs $bootstrapConfigs) : \ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $environment = 'prod' . \random_int(1, 100000);
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel($environment, \ECSPrefix20210517\Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
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
