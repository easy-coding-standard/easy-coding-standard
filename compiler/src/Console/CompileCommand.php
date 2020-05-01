<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Console;

use Nette\Utils\FileSystem as NetteFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Compiler\Composer\ComposerJsonManipulator;
use Symplify\EasyCodingStandard\Compiler\Process\SymfonyProcess;

/**
 * Inspired by @see https://github.com/phpstan/phpstan-src/blob/f939d23155627b5c2ec6eef36d976dddea22c0c5/compiler/src/Console/CompileCommand.php
 */
final class CompileCommand extends Command
{
    /**
     * @var string
     */
    private $dataDir;

    /**
     * @var string
     */
    private $buildDir;

    /**
     * @var ComposerJsonManipulator
     */
    private $composerJsonManipulator;

    public function __construct(string $dataDir, string $buildDir)
    {
        parent::__construct();

        $this->dataDir = $dataDir;
        $this->buildDir = $buildDir;

        $this->composerJsonManipulator = new ComposerJsonManipulator();
    }

    protected function configure(): void
    {
        $this->setName('ecs:compile');
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonFile = $this->buildDir . '/composer.json';

        // remove phpstan.phar, so we can require phpstan/phpstan-src and pack it into phar
        new SymfonyProcess(['composer', 'remove', 'phpstan/phpstan'], $this->buildDir, $output);

        $this->composerJsonManipulator->fixComposerJson($composerJsonFile);
        $this->cleanupPhpCsFixerBreakingFiles();

        // parallel prevention is just for single less-buggy process
        new SymfonyProcess(['php', 'box.phar', 'compile', '--no-parallel'], $this->dataDir, $output);

        $this->composerJsonManipulator->restore();

        // success
        return 0;
    }

    private function cleanupPhpCsFixerBreakingFiles(): void
    {
        // cleanup
        $filesToRemove = [
            __DIR__ . '/../../../vendor/friendsofphp/php-cs-fixer/src/Test/AbstractIntegrationTestCase.php',
        ];

        foreach ($filesToRemove as $fileToRemove) {
            NetteFileSystem::delete($fileToRemove);
        }
    }
}
