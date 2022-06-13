<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix202206\Nette\Utils\FileSystem;
use ECSPrefix202206\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix202206\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix202206\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Exception\DeprecatedException;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(InputInterface $input) : ContainerInterface
    {
        $easyCodingStandardKernel = new EasyCodingStandardKernel();
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
        /** @var ContainerBuilder $container */
        $container = $easyCodingStandardKernel->createFromConfigs($inputConfigFiles);
        $deprecationReporter = new \Symplify\EasyCodingStandard\DependencyInjection\DeprecationReporter();
        $deprecationReporter->reportDeprecatedSets($container, $input);
        $this->reportOldContainerConfiguratorConfig($inputConfigFiles);
        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }
        return $container;
    }
    /**
     * @param string[] $inputConfigFiles
     */
    private function reportOldContainerConfiguratorConfig(array $inputConfigFiles) : void
    {
        foreach ($inputConfigFiles as $inputConfigFile) {
            // warning about old syntax before ECSConfig
            $fileContents = FileSystem::read($inputConfigFile);
            if (\strpos($fileContents, 'ContainerConfigurator $containerConfigurator') === \false) {
                continue;
            }
            $warningMessage = \sprintf('Your "%s" config is using old "ContainerConfigurator".%sUpgrade to "ECSConfig" that allows better autocomplete and future standard. See https://tomasvotruba.com/blog/new-in-ecs-simpler-config/', $inputConfigFile, \PHP_EOL);
            throw new DeprecatedException($warningMessage);
        }
    }
}
