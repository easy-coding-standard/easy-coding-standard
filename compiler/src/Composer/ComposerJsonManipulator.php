<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Compiler\Composer;

use Nette\Utils\FileSystem as NetteFileSystem;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Symfony\Component\Filesystem\Filesystem;
use Symplify\ConsoleColorDiff\Console\Output\ConsoleDiffer;
use Symplify\EasyCodingStandard\Compiler\Packagist\SymplifyStableVersionProvider;

final class ComposerJsonManipulator
{
    /**
     * @var string
     */
    private $originalComposerJsonFileContent;

    /**
     * @var string
     */
    private $composerJsonFilePath;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var SymplifyStableVersionProvider
     */
    private $symplifyStableVersionProvider;

    /**
     * @var ConsoleDiffer
     */
    private $consoleDiffer;

    public function __construct(
        SymplifyStableVersionProvider $symplifyStableVersionProvider,
        Filesystem $filesystem,
        ConsoleDiffer $consoleDiffer
    ) {
        $this->symplifyStableVersionProvider = $symplifyStableVersionProvider;
        $this->filesystem = $filesystem;
        $this->consoleDiffer = $consoleDiffer;
    }

    public function fixComposerJson(string $composerJsonFilePath): void
    {
        $this->composerJsonFilePath = $composerJsonFilePath;

        $fileContent = NetteFileSystem::read($composerJsonFilePath);
        $this->originalComposerJsonFileContent = $fileContent;

        $json = Json::decode($fileContent, Json::FORCE_ARRAY);

        $json = $this->replaceDevSymplifyVersionWithLastStableVersion($json);
        $json = $this->replacePHPStanWithPHPStanSrc($json);
        $json = $this->changeReplace($json);
        $json = $this->fixPhpCodeSnifferAutoloading($json);
        $json = $this->removeDevContent($json);

        // see https://github.com/phpstan/phpstan-src/blob/769669d4ec2a4839cb1aa25a3a29f05aa86b83ed/composer.json#L19
        $encodedJson = Json::encode($json, Json::PRETTY);

        // show diff
        $this->consoleDiffer->diff($this->originalComposerJsonFileContent, $encodedJson);

        $this->filesystem->dumpFile($composerJsonFilePath, $encodedJson);
    }

    /**
     * This prevent root composer.json constant override
     */
    public function restore(): void
    {
        $this->filesystem->dumpFile($this->composerJsonFilePath, $this->originalComposerJsonFileContent);
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

    /**
     * Use phpstan/phpstan-src, because the phpstan.phar cannot be packed into ecs.phar
     */
    private function replacePHPStanWithPHPStanSrc(array $json): array
    {
        // its actually part of coding standard, so we have to require it here
        $json['require']['phpstan/phpstan-src'] = $this->resolveCodingStandardPHPStanVersion();

        $json['repositories'][] = [
            'type' => 'vcs',
            'url' => 'https://github.com/phpstan/phpstan-src.git',
        ];

        // this allows to install vcs
        $json['minimum-stability'] = 'dev';
        $json['prefer-stable'] = true;

        return $json;
    }

    /**
     * This prevent installing packages, that are not needed here.
     */
    private function changeReplace(array $json): array
    {
        $json['replace'] = ['symfony/polyfill-php70' => '*'];

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

    private function removeDevContent(array $json): array
    {
        unset($json['extra'], $json['require-dev'], $json['autoload-dev']);
        return $json;
    }

    private function resolveCodingStandardPHPStanVersion(): string
    {
        $codingStandardFileContent = NetteFileSystem::read(
            __DIR__ . '/../../../../../packages/coding-standard/composer.json'
        );

        $json = Json::decode($codingStandardFileContent, Json::FORCE_ARRAY);

        return (string) $json['require']['phpstan/phpstan'];
    }
}
