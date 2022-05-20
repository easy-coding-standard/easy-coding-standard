<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20220520\Nette\Utils\FileSystem;
use ECSPrefix20220520\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20220520\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20220520\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(\ECSPrefix20220520\Symfony\Component\Console\Input\InputInterface $input) : \ECSPrefix20220520\Symfony\Component\DependencyInjection\ContainerInterface
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
        $this->reportOldContainerConfiguratorConfig($inputConfigFiles, $container);
        if ($inputConfigFiles !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(\Symplify\EasyCodingStandard\Caching\ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFiles);
        }
        return $container;
    }
    /**
     * @param string[] $inputConfigFiles
     */
    private function reportOldContainerConfiguratorConfig(array $inputConfigFiles, \ECSPrefix20220520\Symfony\Component\DependencyInjection\ContainerInterface $container) : void
    {
        foreach ($inputConfigFiles as $inputConfigFile) {
            // warning about old syntax before ECSConfig
            $fileContents = \ECSPrefix20220520\Nette\Utils\FileSystem::read($inputConfigFile);
            if (\strpos($fileContents, 'ContainerConfigurator $containerConfigurator') === \false) {
                continue;
            }
            /** @var SymfonyStyle $symfonyStyle */
            $symfonyStyle = $container->get(\ECSPrefix20220520\Symfony\Component\Console\Style\SymfonyStyle::class);
            // @todo add link to blog post after release
            $warningMessage = \sprintf('Your "%s" config is using old syntax with "ContainerConfigurator".%sPlease upgrade to "ECSConfig" that allows better autocomplete and future standard.', $inputConfigFile, \PHP_EOL);
            $symfonyStyle->warning($warningMessage);
            // to make message noticeable
            \sleep(3);
        }
    }
}
