<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Console;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\EasyCodingStandard\Compiler\Packagist\SymplifyStableVersionProvider;
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
     * @var string
     */
    private $originalComposerJsonFileContent;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymplifyStableVersionProvider
     */
    private $symplifyStableVersionProvider;

    public function __construct(string $dataDir, string $buildDir)
    {
        parent::__construct();

        $this->dataDir = $dataDir;
        $this->buildDir = $buildDir;

        $this->filesystem = new Filesystem();
        $this->symplifyStableVersionProvider = new SymplifyStableVersionProvider();
    }

    protected function configure(): void
    {
        $this->setName('ecs:compile');
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $composerJsonFile = $this->buildDir . '/composer.json';

        $this->fixComposerJson($composerJsonFile);

        // @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/52
        new SymfonyProcess(
            ['composer', 'update', '--no-dev', '--prefer-dist', '--no-interaction', '--classmap-authoritative'],
            $this->buildDir,
            $output
        );

        // parallel prevention is just for single less-buggy process
        new SymfonyProcess(['php', 'box.phar', 'compile', '--no-parallel'], $this->dataDir, $output);

        $this->restoreComposerJson($composerJsonFile);

        return 0;
    }

    private function fixComposerJson(string $composerJsonFile): void
    {
        $fileContent = NetteFileSystem::read($composerJsonFile);
        $this->originalComposerJsonFileContent = $fileContent;

        $json = Json::decode($fileContent, Json::FORCE_ARRAY);

        $json = $this->replaceDevSymplifyVersionWithLastStableVersion($json);
        $json = $this->fixPhpCodeSnifferAutoloading($json);

        $json = $this->removeDevContent($json);
        $this->cleanupPhpCsFixerBreakingFiles();

        $encodedJson = Json::encode($json, Json::PRETTY);

        $this->filesystem->dumpFile($composerJsonFile, $encodedJson);
    }

    /**
     * This prevent root composer.json constant override
     */
    private function restoreComposerJson(string $composerJsonFile): void
    {
        $this->filesystem->dumpFile($composerJsonFile, $this->originalComposerJsonFileContent);

        // re-run @todo composer update on root
    }

    private function replaceDevSymplifyVersionWithLastStableVersion(array $json): array
    {
        $symplifyVersionToRequire = $this->symplifyStableVersionProvider->provide();

        foreach (array_keys($json['require']) as $package) {
            /** @var string $package */
            if (! Strings::startsWith($package, 'symplify/')) {
                continue;
            }

            $json['require'][$package] = $symplifyVersionToRequire;
        }
        return $json;
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

    private function removeDevContent(array $json): array
    {
        $keysToRemove = ['require-dev', 'autoload-dev', 'minimum-stability', 'prefer-stable', 'extra'];

        foreach ($keysToRemove as $keyToRemove) {
            unset($json[$keyToRemove]);
        }

        return $json;
    }

    /**
     * Their autoloader is broken inside the phar :/
     */
    private function fixPhpCodeSnifferAutoloading(array $json): array
    {
        $json['autoload']['classmap'][] = 'vendor/squizlabs/php_codesniffer/src';

        return $json;
    }
}
