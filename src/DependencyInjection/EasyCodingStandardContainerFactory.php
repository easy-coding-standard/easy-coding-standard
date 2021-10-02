<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211002\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use ECSPrefix20211002\Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(\ECSPrefix20211002\Symfony\Component\Console\Input\InputInterface $input) : \ECSPrefix20211002\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $environment = $this->resolveEnvironment();
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel($environment, \ECSPrefix20211002\Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
        $inputConfigFileInfos = [];
        $rootECSConfig = \getcwd() . \DIRECTORY_SEPARATOR . '/ecs.php';
        if ($input->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $input->getParameterOption(['--config', '-c']);
            if (\is_string($commandLineConfigFile) && \file_exists($commandLineConfigFile)) {
                $inputConfigFileInfos[] = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($commandLineConfigFile);
            }
        } elseif (\file_exists($rootECSConfig)) {
            $inputConfigFileInfos[] = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($rootECSConfig);
        }
        if ($inputConfigFileInfos !== []) {
            $easyCodingStandardKernel->setConfigs($inputConfigFileInfos);
        }
        $easyCodingStandardKernel->boot();
        $container = $easyCodingStandardKernel->getContainer();
        if ($inputConfigFileInfos !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFileInfos);
        }
        return $container;
    }
    private function resolveEnvironment() : string
    {
        if (\Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION === '@package_version@') {
            return 'dev';
        }
        return 'prod_' . \Symplify\EasyCodingStandard\Application\Version\StaticVersionResolver::PACKAGE_VERSION;
    }
}
