<?php

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

use ECSPrefix20210512\Composer\Semver\Comparator;
use ECSPrefix20210512\Composer\Semver\Semver;
use ECSPrefix20210512\Composer\Semver\VersionParser;
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
        $this->versionParser = new \ECSPrefix20210512\Composer\Semver\VersionParser();
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function getLatestVersion()
    {
        $this->retrieveAvailableVersions();
        return $this->availableVersions[0];
    }
    /**
     * {@inheritdoc}
     * @return string|null
     * @param int $majorVersion
     */
    public function getLatestVersionOfMajor($majorVersion)
    {
        $majorVersion = (int) $majorVersion;
        $this->retrieveAvailableVersions();
        $semverConstraint = '^' . $majorVersion;
        foreach ($this->availableVersions as $availableVersion) {
            if (\ECSPrefix20210512\Composer\Semver\Semver::satisfies($availableVersion, $semverConstraint)) {
                return $availableVersion;
            }
        }
        return null;
    }
    /**
     * {@inheritdoc}
     * @param string $versionA
     * @param string $versionB
     * @return int
     */
    public function compareVersions($versionA, $versionB)
    {
        $versionA = (string) $versionA;
        $versionB = (string) $versionB;
        $versionA = $this->versionParser->normalize($versionA);
        $versionB = $this->versionParser->normalize($versionB);
        if (\ECSPrefix20210512\Composer\Semver\Comparator::lessThan($versionA, $versionB)) {
            return -1;
        }
        if (\ECSPrefix20210512\Composer\Semver\Comparator::greaterThan($versionA, $versionB)) {
            return 1;
        }
        return 0;
    }
    /**
     * @return void
     */
    private function retrieveAvailableVersions()
    {
        if (null !== $this->availableVersions) {
            return;
        }
        foreach ($this->githubClient->getTags() as $tag) {
            $version = $tag['name'];
            try {
                $this->versionParser->normalize($version);
                if ('stable' === \ECSPrefix20210512\Composer\Semver\VersionParser::parseStability($version)) {
                    $this->availableVersions[] = $version;
                }
            } catch (\UnexpectedValueException $exception) {
                // not a valid version tag
            }
        }
        $this->availableVersions = \ECSPrefix20210512\Composer\Semver\Semver::rsort($this->availableVersions);
    }
}
