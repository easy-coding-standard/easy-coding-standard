<?php

namespace ECSPrefix20210517\Symplify\SmartFileSystem\Finder;

use ECSPrefix20210517\Symfony\Component\Finder\Finder;
use ECSPrefix20210517\Symplify\SmartFileSystem\FileSystemFilter;
use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\SmartFileSystem\Tests\Finder\SmartFinder\SmartFinderTest
 */
final class SmartFinder
{
    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @var FileSystemFilter
     */
    private $fileSystemFilter;
    public function __construct(\ECSPrefix20210517\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer, \ECSPrefix20210517\Symplify\SmartFileSystem\FileSystemFilter $fileSystemFilter)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileSystemFilter = $fileSystemFilter;
    }
    /**
     * @param string[] $directoriesOrFiles
     * @return mixed[]
     * @param string $path
     */
    public function findPaths(array $directoriesOrFiles, $path)
    {
        $path = (string) $path;
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);
        $fileInfos = [];
        if ($directories !== []) {
            $finder = new \ECSPrefix20210517\Symfony\Component\Finder\Finder();
            $finder->name('*')->in($directories)->path($path)->files()->sortByName();
            $fileInfos = $this->finderSanitizer->sanitize($finder);
        }
        return $fileInfos;
    }
    /**
     * @param string[] $directoriesOrFiles
     * @param string[] $excludedDirectories
     * @return mixed[]
     * @param string $name
     */
    public function find(array $directoriesOrFiles, $name, array $excludedDirectories = [])
    {
        $name = (string) $name;
        $directories = $this->fileSystemFilter->filterDirectories($directoriesOrFiles);
        $fileInfos = [];
        if ($directories !== []) {
            $finder = new \ECSPrefix20210517\Symfony\Component\Finder\Finder();
            $finder->name($name)->in($directories)->files()->sortByName();
            if ($excludedDirectories !== []) {
                $finder->exclude($excludedDirectories);
            }
            $fileInfos = $this->finderSanitizer->sanitize($finder);
        }
        $files = $this->fileSystemFilter->filterFiles($directoriesOrFiles);
        foreach ($files as $file) {
            $fileInfos[] = new \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo($file);
        }
        return $fileInfos;
    }
}
