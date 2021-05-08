<?php

namespace Symplify\EasyTesting\DataProvider;

use Iterator;
use ECSPrefix20210508\Nette\Utils\Strings;
use ECSPrefix20210508\Symfony\Component\Finder\Finder;
use ECSPrefix20210508\Symfony\Component\Finder\SplFileInfo;
use Symplify\SmartFileSystem\Exception\FileNotFoundException;
use Symplify\SmartFileSystem\SmartFileInfo;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\EasyTesting\Tests\DataProvider\StaticFixtureFinder\StaticFixtureFinderTest
 */
final class StaticFixtureFinder
{
    /**
     * @return Iterator<array<int, SmartFileInfo>>
     * @param string $directory
     */
    public static function yieldDirectory($directory, string $suffix = '*.php.inc') : \Iterator
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $fileInfos = self::findFilesInDirectory($directory, $suffix);
        return self::yieldFileInfos($fileInfos);
    }
    /**
     * @return Iterator<SmartFileInfo>
     * @param string $directory
     */
    public static function yieldDirectoryExclusively($directory, string $suffix = '*.php.inc') : \Iterator
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $fileInfos = self::findFilesInDirectoryExclusively($directory, $suffix);
        return self::yieldFileInfos($fileInfos);
    }
    /**
     * @return Iterator<string, array<int, SplFileInfo>>
     * @param string $directory
     */
    public static function yieldDirectoryWithRelativePathname($directory, string $suffix = '*.php.inc') : \Iterator
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $fileInfos = self::findFilesInDirectory($directory, $suffix);
        return self::yieldFileInfosWithRelativePathname($fileInfos);
    }
    /**
     * @return Iterator<string, array<int, SplFileInfo>>
     * @param string $directory
     */
    public static function yieldDirectoryExclusivelyWithRelativePathname($directory, string $suffix = '*.php.inc') : \Iterator
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $fileInfos = self::findFilesInDirectoryExclusively($directory, $suffix);
        return self::yieldFileInfosWithRelativePathname($fileInfos);
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return \Iterator
     */
    private static function yieldFileInfos(array $fileInfos)
    {
        foreach ($fileInfos as $fileInfo) {
            try {
                $smartFileInfo = new \Symplify\SmartFileSystem\SmartFileInfo($fileInfo->getRealPath());
                (yield [$smartFileInfo]);
            } catch (\Symplify\SmartFileSystem\Exception\FileNotFoundException $fileNotFoundException) {
            }
        }
    }
    /**
     * @param SplFileInfo[] $fileInfos
     * @return \Iterator
     */
    private static function yieldFileInfosWithRelativePathname(array $fileInfos)
    {
        foreach ($fileInfos as $fileInfo) {
            try {
                $smartFileInfo = new \Symplify\SmartFileSystem\SmartFileInfo($fileInfo->getRealPath());
                (yield $fileInfo->getRelativePathname() => [$smartFileInfo]);
            } catch (\Symplify\SmartFileSystem\Exception\FileNotFoundException $e) {
            }
        }
    }
    /**
     * @return SplFileInfo[]
     * @param string $directory
     */
    private static function findFilesInDirectory($directory, string $suffix) : array
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $finder = \ECSPrefix20210508\Symfony\Component\Finder\Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = \iterator_to_array($finder);
        return \array_values($fileInfos);
    }
    /**
     * @return SplFileInfo[]
     * @param string $directory
     */
    private static function findFilesInDirectoryExclusively($directory, string $suffix) : array
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        self::ensureNoOtherFileName($directory, $suffix);
        $finder = \ECSPrefix20210508\Symfony\Component\Finder\Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = \iterator_to_array($finder->getIterator());
        return \array_values($fileInfos);
    }
    /**
     * @return void
     * @param string $directory
     */
    private static function ensureNoOtherFileName($directory, string $suffix)
    {
        if (\is_object($directory)) {
            $directory = (string) $directory;
        }
        $iterator = \ECSPrefix20210508\Symfony\Component\Finder\Finder::create()->in($directory)->files()->notName($suffix)->getIterator();
        $relativeFilePaths = [];
        foreach ($iterator as $fileInfo) {
            $relativeFilePaths[] = \ECSPrefix20210508\Nette\Utils\Strings::substring($fileInfo->getRealPath(), \strlen(\getcwd()) + 1);
        }
        if ($relativeFilePaths === []) {
            return;
        }
        throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException(\sprintf('Files "%s" have invalid suffix, use "%s" suffix instead', \implode('", ', $relativeFilePaths), $suffix));
    }
}
