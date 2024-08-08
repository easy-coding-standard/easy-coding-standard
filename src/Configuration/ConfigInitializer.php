<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Configuration;

use ECSPrefix202408\Nette\Utils\FileSystem;
use ECSPrefix202408\Symfony\Component\Console\Style\SymfonyStyle;
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
    public function __construct(FileProcessorCollector $fileProcessorCollector, SymfonyStyle $symfonyStyle, \Symplify\EasyCodingStandard\Configuration\InitPathsResolver $initPathsResolver, \ECSPrefix202408\Symfony\Component\Filesystem\Filesystem $filesystem)
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
            $this->symfonyStyle->warning('We found ecs.php config, but with no rules in it. Register some rules or sets there first');
            return;
        }
        $response = $this->symfonyStyle->ask('No "ecs.php" config found. Should we generate it for you?', 'yes');
        // be tolerant about input
        if (!\in_array($response, ['yes', 'YES', 'y', 'Y'], \true)) {
            // okay, nothing we can do
            return;
        }
        $templateFileContents = FileSystem::read(__DIR__ . '/../../templates/ecs.php.dist');
        $projectPhpDirectories = $this->initPathsResolver->resolve($projectDirectory);
        $projectPhpDirectoriesContents = $this->createPathsString($projectPhpDirectories);
        $templateFileContents = \str_replace('__PATHS__', $projectPhpDirectoriesContents, $templateFileContents);
        // create the ecs.php file
        FileSystem::write(\getcwd() . '/ecs.php', $templateFileContents, null);
        $this->symfonyStyle->success('The ecs.php config was generated! Re-run the command to tidy your code');
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
