<?php

namespace Symplify\EasyTesting\PHPUnit\Behavior;

use ECSPrefix20210508\Symfony\Component\Finder\Finder;
use Symplify\EasyTesting\ValueObject\ExpectedAndOutputFileInfoPair;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
/**
 * Use only in "\PHPUnit\Framework\TestCase"
 *
 * Answer here
 *
 * @see https://stackoverflow.com/questions/54263109/how-to-assert-2-directories-are-identical-in-phpunit
 */
trait DirectoryAssertableTrait
{
    /**
     * @return void
     * @param string $expectedDirectory
     * @param string $outputDirectory
     */
    protected function assertDirectoryEquals($expectedDirectory, $outputDirectory)
    {
        if (\is_object($outputDirectory)) {
            $outputDirectory = (string) $outputDirectory;
        }
        if (\is_object($expectedDirectory)) {
            $expectedDirectory = (string) $expectedDirectory;
        }
        $expectedFileInfos = $this->findFileInfosInDirectory($expectedDirectory);
        $outputFileInfos = $this->findFileInfosInDirectory($outputDirectory);
        $fileInfosByRelativeFilePath = $this->groupFileInfosByRelativeFilePath($expectedFileInfos, $expectedDirectory, $outputFileInfos, $outputDirectory);
        foreach ($fileInfosByRelativeFilePath as $relativeFilePath => $expectedAndOutputFileInfoPair) {
            // output file exists
            $this->assertFileExists($outputDirectory . '/' . $relativeFilePath);
            if (!$expectedAndOutputFileInfoPair->doesOutputFileExist()) {
                continue;
            }
            // they have the same content
            $this->assertSame($expectedAndOutputFileInfoPair->getExpectedFileContent(), $expectedAndOutputFileInfoPair->getOutputFileContent(), $relativeFilePath);
        }
    }
    /**
     * @return mixed[]
     * @param string $directory
     */
    private function findFileInfosInDirectory($directory)
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $firstDirectoryFinder = new \ECSPrefix20210508\Symfony\Component\Finder\Finder();
        $firstDirectoryFinder->files()->in($directory);
        $finderSanitizer = new \Symplify\SmartFileSystem\Finder\FinderSanitizer();
        return $finderSanitizer->sanitize($firstDirectoryFinder);
    }
    /**
     * @param SmartFileInfo[] $expectedFileInfos
     * @param SmartFileInfo[] $outputFileInfos
     * @return mixed[]
     * @param string $expectedDirectory
     * @param string $outputDirectory
     */
    private function groupFileInfosByRelativeFilePath(array $expectedFileInfos, $expectedDirectory, array $outputFileInfos, $outputDirectory)
    {
        if (\is_object($outputDirectory)) {
            $outputDirectory = (string) $outputDirectory;
        }
        if (\is_object($expectedDirectory)) {
            $expectedDirectory = (string) $expectedDirectory;
        }
        $fileInfosByRelativeFilePath = [];
        foreach ($expectedFileInfos as $expectedFileInfo) {
            $relativeFilePath = $expectedFileInfo->getRelativeFilePathFromDirectory($expectedDirectory);
            // match output file info
            $outputFileInfo = $this->resolveFileInfoByRelativeFilePath($outputFileInfos, $outputDirectory, $relativeFilePath);
            $fileInfosByRelativeFilePath[$relativeFilePath] = new \Symplify\EasyTesting\ValueObject\ExpectedAndOutputFileInfoPair($expectedFileInfo, $outputFileInfo);
        }
        return $fileInfosByRelativeFilePath;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return \Symplify\SmartFileSystem\SmartFileInfo|null
     * @param string $directory
     * @param string $desiredRelativeFilePath
     */
    private function resolveFileInfoByRelativeFilePath(array $fileInfos, $directory, $desiredRelativeFilePath)
    {
        if (\is_object($desiredRelativeFilePath)) {
            $desiredRelativeFilePath = (string) $desiredRelativeFilePath;
        }
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        foreach ($fileInfos as $fileInfo) {
            $relativeFilePath = $fileInfo->getRelativeFilePathFromDirectory($directory);
            if ($desiredRelativeFilePath !== $relativeFilePath) {
                continue;
            }
            return $fileInfo;
        }
        return null;
    }
}
