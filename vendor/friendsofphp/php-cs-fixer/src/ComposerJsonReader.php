<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer;

use ECSPrefix202510\Composer\Semver\Semver;
use ECSPrefix202510\Symfony\Component\Filesystem\Exception\IOException;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ComposerJsonReader
{
    private const COMPOSER_FILENAME = 'composer.json';
    /**
     * @var bool
     */
    private $isProcessed = \false;
    /**
     * @var string|null
     */
    private $php;
    /**
     * @var string|null
     */
    private $phpUnit;
    public static function createSingleton() : self
    {
        static $instance = null;
        if (!$instance) {
            $instance = new self();
        }
        return $instance;
    }
    public function getPhp() : ?string
    {
        $this->processFile();
        return $this->php;
    }
    public function getPhpUnit() : ?string
    {
        $this->processFile();
        return $this->phpUnit;
    }
    private function processFile() : void
    {
        if (\true === $this->isProcessed) {
            return;
        }
        if (!\file_exists(self::COMPOSER_FILENAME)) {
            throw new IOException(\sprintf('Failed to read file "%s".', self::COMPOSER_FILENAME));
        }
        $readResult = \file_get_contents(self::COMPOSER_FILENAME);
        if (\false === $readResult) {
            throw new IOException(\sprintf('Failed to read file "%s".', self::COMPOSER_FILENAME));
        }
        $this->processJson($readResult);
    }
    private function processJson(string $json) : void
    {
        if (\true === $this->isProcessed) {
            return;
        }
        $composerJson = \json_decode($json, \true, 512, 0);
        if (\json_last_error() !== \JSON_ERROR_NONE) {
            throw new \Exception(\json_last_error_msg());
        }
        $this->php = self::getMinSemVer(self::detectPhp($composerJson));
        $this->phpUnit = self::getMinSemVer(self::detectPackage($composerJson, 'phpunit/phpunit'));
        $this->isProcessed = \true;
    }
    private static function getMinSemVer(?string $version) : ?string
    {
        if ('' === $version || null === $version) {
            return null;
        }
        /** @var non-empty-list<string> $arr */
        $arr = \PhpCsFixer\Preg::split('/\\s*\\|\\|?\\s*/', \trim($version));
        $arr = \array_map(static function ($v) {
            $v = \ltrim($v, '^~>=');
            if (\substr_compare($v, '.*', -\strlen('.*')) === 0) {
                $v = (string) \substr($v, 0, -\strlen('.*'));
            }
            $space = \strpos($v, ' ');
            if (\false !== $space) {
                $v = (string) \substr($v, 0, $space);
            }
            return $v;
        }, $arr);
        $textVersion = null;
        foreach ($arr as $v) {
            if (\true === \PhpCsFixer\Preg::match('/^\\D/', $v)) {
                $textVersion = $v;
                break;
            }
        }
        if (null !== $textVersion) {
            return null;
        }
        /** @var non-empty-list<string> $sortedArr */
        $sortedArr = Semver::sort($arr);
        $min = $sortedArr[0];
        $parts = \explode('.', $min);
        return \sprintf('%s.%s', (int) $parts[0], (int) ($parts[1] ?? 0));
    }
    /**
     * @param array<string, mixed> $composerJson
     */
    private static function detectPhp(array $composerJson) : ?string
    {
        $version = [];
        if (isset($composerJson['config']['platform']['php'])) {
            $version[] = $composerJson['config']['platform']['php'];
        }
        if (isset($composerJson['require-dev']['php'])) {
            $version[] = $composerJson['require-dev']['php'];
        }
        if (isset($composerJson['require']['php'])) {
            $version[] = $composerJson['require']['php'];
        }
        if (\count($version) > 0) {
            return \implode(' || ', $version);
        }
        return null;
    }
    /**
     * @param array<string, mixed> $composerJson
     * @param non-empty-string     $package
     */
    private static function detectPackage(array $composerJson, string $package) : ?string
    {
        $version = [];
        if (isset($composerJson['require-dev'][$package])) {
            $version[] = $composerJson['require-dev'][$package];
        }
        if (isset($composerJson['require'][$package])) {
            $version[] = $composerJson['require'][$package];
        }
        if (\count($version) > 0) {
            return \implode(' || ', $version);
        }
        return null;
    }
}
