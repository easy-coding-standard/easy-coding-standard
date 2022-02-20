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
namespace PhpCsFixer\Console\SelfUpdate;

use ECSPrefix20220220\Composer\Semver\Comparator;
use ECSPrefix20220220\Composer\Semver\Semver;
use ECSPrefix20220220\Composer\Semver\VersionParser;
/**
 * @internal
 */
final class NewVersionChecker implements \PhpCsFixer\Console\SelfUpdate\NewVersionCheckerInterface
{
    /**
     * @var GithubClientInterface
     */
    private $githubClient;
    /**
     * @var VersionParser
     */
    private $versionParser;
    /**
     * @var null|string[]
     */
    private $availableVersions;
    public function __construct(\PhpCsFixer\Console\SelfUpdate\GithubClientInterface $githubClient)
    {
        $this->githubClient = $githubClient;
        $this->versionParser = new \ECSPrefix20220220\Composer\Semver\VersionParser();
    }
    /**
     * {@inheritdoc}
     */
    public function getLatestVersion() : string
    {
        $this->retrieveAvailableVersions();
        return $this->availableVersions[0];
    }
    /**
     * {@inheritdoc}
     */
    public function getLatestVersionOfMajor(int $majorVersion) : ?string
    {
        $this->retrieveAvailableVersions();
        $semverConstraint = '^' . $majorVersion;
        foreach ($this->availableVersions as $availableVersion) {
            if (\ECSPrefix20220220\Composer\Semver\Semver::satisfies($availableVersion, $semverConstraint)) {
                return $availableVersion;
            }
        }
        return null;
    }
    /**
     * {@inheritdoc}
     */
    public function compareVersions(string $versionA, string $versionB) : int
    {
        $versionA = $this->versionParser->normalize($versionA);
        $versionB = $this->versionParser->normalize($versionB);
        if (\ECSPrefix20220220\Composer\Semver\Comparator::lessThan($versionA, $versionB)) {
            return -1;
        }
        if (\ECSPrefix20220220\Composer\Semver\Comparator::greaterThan($versionA, $versionB)) {
            return 1;
        }
        return 0;
    }
    private function retrieveAvailableVersions() : void
    {
        if (null !== $this->availableVersions) {
            return;
        }
        foreach ($this->githubClient->getTags() as $tag) {
            $version = $tag['name'];
            try {
                $this->versionParser->normalize($version);
                if ('stable' === \ECSPrefix20220220\Composer\Semver\VersionParser::parseStability($version)) {
                    $this->availableVersions[] = $version;
                }
            } catch (\UnexpectedValueException $exception) {
                // not a valid version tag
            }
        }
        $this->availableVersions = \ECSPrefix20220220\Composer\Semver\Semver::rsort($this->availableVersions);
    }
}
