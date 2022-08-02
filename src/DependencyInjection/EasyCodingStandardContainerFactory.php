<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix202208\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix202208\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202208\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(ArgvInput $argvInput) : ContainerInterface
    {
        $easyCodingStandardKernel = new EasyCodingStandardKernel();
        $inputConfigFiles = [];
        $rootECSConfig = \getcwd() . \DIRECTORY_SEPARATOR . 'ecs.php';
        if ($argvInput->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $argvInput->getParameterOption(['--config', '-c']);
            if (\is_string($commandLineConfigFile) && \file_exists($commandLineConfigFile)) {
                // must be realpath, so container builder knows the location
                $inputConfigFiles[] = (string) \realpath($commandLineConfigFile);
            }
        } elseif (\file_exists($rootECSConfig)) {
            $inputConfigFiles[] = $rootECSConfig;
        }
        /** @var ContainerBuilder $container */
        $container = $easyCodingStandardKernel->createFromConfigs($inputConfigFiles);
        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }
        return $container;
    }
}
