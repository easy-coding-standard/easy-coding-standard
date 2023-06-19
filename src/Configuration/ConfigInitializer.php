<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix202306\Nette\Utils\FileSystem;
use ECSPrefix202306\Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
final class ConfigInitializer
{
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Application\FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @readonly
     * @var \Symfony\Component\Console\Style\SymfonyStyle
     */
    private $symfonyStyle;
    /**
     * @readonly
     * @var \Symplify\EasyCodingStandard\Configuration\InitPathsResolver
     */
    private $initPathsResolver;
    /**
     * @readonly
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;
    public function __construct(FileProcessorCollector $fileProcessorCollector, SymfonyStyle $symfonyStyle, \Symplify\EasyCodingStandard\Configuration\InitPathsResolver $initPathsResolver, \ECSPrefix202306\Symfony\Component\Filesystem\Filesystem $filesystem)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
        $this->symfonyStyle = $symfonyStyle;
        $this->initPathsResolver = $initPathsResolver;
        $this->filesystem = $filesystem;
    }
    public function areSomeCheckersRegistered() : bool
    {
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers()) {
                return \true;
            }
        }
        return \false;
    }
    public function createConfig(string $projectDirectory) : void
    {
        $doesConfigExist = $this->filesystem->exists($projectDirectory . '/ecs.php');
        // config already exists, nothing to add
        if ($doesConfigExist) {
            $this->symfonyStyle->warning('The "ecs.php" config already exists. Register rules or sets there to make it change the code.');
            return;
        }
        $response = $this->symfonyStyle->ask('No "ecs.php" config found. Should we generate it for you?', 'yes');
        if ($response !== 'yes') {
            // okay, nothing we can do
            return;
        }
        $templateFileContents = FileSystem::read(__DIR__ . '/../../templates/ecs.php.dist');
        $projectPhpDirectories = $this->initPathsResolver->resolve($projectDirectory);
        $projectPhpDirectoriesContents = $this->createPathsString($projectPhpDirectories);
        $templateFileContents = \str_replace('__PATHS__', $projectPhpDirectoriesContents, $templateFileContents);
        // write the contents :)
        FileSystem::write(\getcwd() . '/ecs.php', $templateFileContents);
        $this->symfonyStyle->success('The config file was generated! Now re-run the command to make your code tidy');
    }
    /**
     * @param string[] $projectPhpDirectories
     */
    private function createPathsString(array $projectPhpDirectories) : string
    {
        $projectPhpDirectoriesContents = '';
        foreach ($projectPhpDirectories as $projectPhpDirectory) {
            $projectPhpDirectoriesContents .= "        __DIR__ . '/" . $projectPhpDirectory . "'," . \PHP_EOL;
        }
        return \rtrim($projectPhpDirectoriesContents);
    }
}
