<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Console;

use Nette\Utils\FileSystem as NetteFileSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Compiler\Composer\ComposerJsonManipulator;
use Symplify\EasyCodingStandard\Compiler\Process\SymfonyProcess;
use Symplify\PackageBuilder\Console\Style\SymfonyStyleFactory;
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

    public function __construct(string $dataDir, string $buildDir)
    {
        parent::__construct();

        $this->dataDir = $dataDir;
        $this->buildDir = $buildDir;

        $this->composerJsonManipulator = new ComposerJsonManipulator();
        $symfonyStyleFactory = new SymfonyStyleFactory();
        $this->symfonyStyle = $symfonyStyleFactory->create();
    }

    protected function configure(): void
    {
        $this->setName('ecs:compile');
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonFile = $this->buildDir . '/composer.json';

        $composerJsonFileInfo = new SmartFileInfo($composerJsonFile);

        // remove phpstan.phar, so we can require phpstan/phpstan-src and pack it into phar
        $this->symfonyStyle->note(sprintf('Reading "%s" file', $composerJsonFileInfo));
        $this->composerJsonManipulator->fixComposerJson($composerJsonFile);
        $this->cleanupPhpCsFixerBreakingFiles();

        $this->symfonyStyle->section('Running "composer update" for new config');
        // @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/52
        new SymfonyProcess(
            ['composer', 'update', '--no-dev', '--prefer-dist', '--no-interaction', '--classmap-authoritative'],
            $this->buildDir,
            $output
        );

        // parallel prevention is just for single less-buggy process
        $this->symfonyStyle->section('Packing and prefixing ecs.phar with Box and PHP Scoper');
        new SymfonyProcess(['php', 'box.phar', 'compile', '--no-parallel'], $this->dataDir, $output);

        $this->symfonyStyle->note('Restoring original composer.json content');
        $this->composerJsonManipulator->restore();

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
