<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(\ECSPrefix20220220\Symfony\Component\Console\Input\InputInterface $input) : \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel();
        $inputConfigFiles = [];
        $rootECSConfig = \getcwd() . \DIRECTORY_SEPARATOR . 'ecs.php';
        if ($input->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $input->getParameterOption(['--config', '-c']);
            if (\is_string($commandLineConfigFile) && \file_exists($commandLineConfigFile)) {
                // must be realpath, so container builder knows the location
                $inputConfigFiles[] = (string) \realpath($commandLineConfigFile);
            }
        } elseif (\file_exists($rootECSConfig)) {
            $inputConfigFiles[] = $rootECSConfig;
        }
        $container = $easyCodingStandardKernel->createFromConfigs($inputConfigFiles);
        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }
        return $container;
    }
}
