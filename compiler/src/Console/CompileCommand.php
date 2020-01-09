<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Console;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use PharIo\Version\Version;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\EasyCodingStandard\Compiler\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\Compiler\Process\CompileProcessFactory;

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
     * @var string|null
     */
    private $symplifyVersionToRequire;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var CompileProcessFactory
     */
    private $compileProcessFactory;

    public function __construct(CompileProcessFactory $compileProcessFactory, string $dataDir, string $buildDir)
    {
        parent::__construct();
        $this->filesystem = new Filesystem();
        $this->compileProcessFactory = $compileProcessFactory;
        $this->dataDir = $dataDir;
        $this->buildDir = $buildDir;
    }

    protected function configure(): void
    {
        $this->setName('ecs:compile');
        $this->setDescription('Compile prefixed ecs.phar');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->compileProcessFactory->setOutput($output);

        $composerJsonFile = $this->buildDir . '/composer.json';

        $this->fixComposerJson($composerJsonFile);
        // @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/52
        $this->compileProcessFactory->create(
            ['composer', 'update', '--no-dev', '--prefer-dist', '--no-interaction', '--classmap-authoritative'],
            $this->buildDir
        );

        // remove bugging packages
        $dirsToRemove = [__DIR__ . '/../../../vendor/symfony/polyfill-php70'];
        foreach ($dirsToRemove as $dirToRemove) {
            NetteFileSystem::delete($dirToRemove);
        }

        // parallel prevention is just for single less-buggy process
        $this->compileProcessFactory->create(['php', 'box.phar', 'compile', '--no-parallel'], $this->dataDir);

        $this->restoreComposerJson($composerJsonFile);

        return 0;
    }

    private function fixComposerJson(string $composerJsonFile): void
    {
        $fileContent = NetteFileSystem::read($composerJsonFile);
        $this->originalComposerJsonFileContent = $fileContent;

        $json = Json::decode($fileContent, Json::FORCE_ARRAY);

        // remove dev dependencies (they create conflicts)
        unset($json['require-dev'], $json['autoload-dev']);

        // simplify autoload (remove not packed build directory]
        $json['autoload']['psr-4']['Symplify\\EasyCodingStandard\\'] = 'src';

        // remove dev content
        unset($json['minimum-stability']);
        unset($json['prefer-stable']);
        unset($json['extra']);

        // use stable version for symplify packages
        foreach (array_keys($json['require']) as $package) {
            /** @var string $package */
            if (! Strings::startsWith($package, 'symplify/')) {
                continue;
            }

            $symplifyVersionToRequire = $this->getSymplifyStableVersionToRequire();
            $json['require'][$package] = $symplifyVersionToRequire;
        }

        // cleanup
        $filesToRemove = [
            __DIR__ . '/../../../vendor/friendsofphp/php-cs-fixer/src/Test/AbstractIntegrationTestCase.php',
        ];

        foreach ($filesToRemove as $fileToRemove) {
            NetteFileSystem::delete($fileToRemove);
        }

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

    private function getSymplifyStableVersionToRequire(): string
    {
        if ($this->symplifyVersionToRequire !== null) {
            return $this->symplifyVersionToRequire;
        }

        $symplifyPackageContent = file_get_contents('https://repo.packagist.org/p/symplify/symplify.json');
        if ($symplifyPackageContent === null) {
            throw new ShouldNotHappenException();
        }

        $symplifyPackageJson = Json::decode($symplifyPackageContent, Json::FORCE_ARRAY);
        $symplifyPackageVersions = $symplifyPackageJson['packages']['symplify/symplify'];
        end($symplifyPackageVersions);

        $lastStableVersion = key($symplifyPackageVersions);
        $lastStableVersion = new Version($lastStableVersion);

        $this->symplifyVersionToRequire = '^' . $lastStableVersion->getMajor()->getValue() . '.' . $lastStableVersion->getMinor()->getValue();

        return $this->symplifyVersionToRequire;
    }
}
