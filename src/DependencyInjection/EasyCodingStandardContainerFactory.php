<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix202309\Illuminate\Container\Container;
use ECSPrefix202309\Symfony\Component\Console\Input\ArgvInput;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
/**
 * @api
 */
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(ArgvInput $argvInput) : Container
    {
        // $easyCodingStandardKernel = new EasyCodingStandardKernel();
        $newContainerFactory = new \Symplify\EasyCodingStandard\DependencyInjection\NewContainerFactory();
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
        $container = $newContainerFactory->create($inputConfigFiles);
        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->make(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }
        return $container;
    }
}
