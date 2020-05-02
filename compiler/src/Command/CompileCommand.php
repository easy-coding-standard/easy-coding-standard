<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Command;

use Nette\Utils\FileSystem as NetteFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Compiler\Composer\ComposerJsonManipulator;
use Symplify\EasyCodingStandard\Compiler\Process\SymfonyProcess;
use Symplify\SmartFileSystem\SmartFileInfo;

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

    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    public function __construct(
        string $dataDir,
        string $buildDir,
        SymfonyStyle $symfonyStyle,
        ComposerJsonManipulator $composerJsonManipulator
    ) {
        parent::__construct();

        $this->dataDir = $dataDir;
        $this->buildDir = $buildDir;

        $this->symfonyStyle = $symfonyStyle;
        $this->composerJsonManipulator = $composerJsonManipulator;
    }

    protected function configure(): void
    {
        $this->setName('ecs:compile');
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonFileInfo = new SmartFileInfo($this->buildDir . '/composer.json');

        // 1.
        $this->symfonyStyle->section(
            sprintf('1. Loading and updating "%s"', $composerJsonFileInfo->getRelativeFilePathFromCwd())
        );

        $this->composerJsonManipulator->fixComposerJson($composerJsonFileInfo);
        $this->cleanupPhpCsFixerBreakingFiles();

        // 2.
        $this->symfonyStyle->section('2. Running "composer update" for new config');
        // @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/52
        new SymfonyProcess(
            [
                'composer',
                'update',
                '--no-dev',
                '--prefer-dist',
                '--no-interaction',
                '--classmap-authoritative',
                '--ansi',
            ],
            $this->buildDir,
            $output
        );

        // 3.
        // parallel prevention is just for single less-buggy process
        $this->symfonyStyle->section('3. Packing and prefixing ecs.phar with Box and PHP Scoper');
        new SymfonyProcess(['php', 'box.phar', 'compile', '--no-parallel', '--ansi'], $this->dataDir, $output);

        // 4.
        $this->symfonyStyle->section('4. Restoring original composer.json content');
        $this->composerJsonManipulator->restore();
        $this->symfonyStyle->note('You still need to run "composer update" to install those dependencies');

        $this->symfonyStyle->success('ecs.phar was generated');

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
