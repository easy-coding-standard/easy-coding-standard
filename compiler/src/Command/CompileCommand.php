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

/**
 * Inspired by @see https://github.com/phpstan/phpstan-src/blob/f939d23155627b5c2ec6eef36d976dddea22c0c5/compiler/src/Console/CompileCommand.php
 */
final class CompileCommand extends Command
{
    /**
     * @var string
     */
    public const NAME = 'ecs:compile';

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
        $this->setName(self::NAME);
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $composerJsonFilePath */
        $composerJsonFilePath = realpath($this->buildDir . '/composer.json');

        $this->symfonyStyle->title(sprintf('1. Loading and updating "%s"', realpath($composerJsonFilePath)));

        $this->composerJsonManipulator->fixComposerJson($composerJsonFilePath);
        $this->cleanupPhpCsFixerBreakingFiles();

        $this->symfonyStyle->title('2. Running "composer update" for new config');
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

        $this->symfonyStyle->title('3. Packing and prefixing ecs.phar with Box and PHP Scoper');
        // parallel prevention is just for single less-buggy process
        new SymfonyProcess(['php', 'box.phar', 'compile', '--no-parallel', '--ansi'], $this->dataDir, $output);

        $this->symfonyStyle->title('4. Restoring original "composer.json" content');
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
