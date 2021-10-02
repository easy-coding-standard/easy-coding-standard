<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Finder;

use ECSPrefix20211002\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Git\GitDiffProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20211002\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20211002\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Finder\SourceFinderTest
 */
final class SourceFinder
{
    /**
     * @var string[]
     */
    private $fileExtensions = [];
    /**
     * @var \Symplify\SmartFileSystem\Finder\FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @var \Symplify\EasyCodingStandard\Git\GitDiffProvider
     */
    private $gitDiffProvider;
    public function __construct(\ECSPrefix20211002\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer, \ECSPrefix20211002\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider, \Symplify\EasyCodingStandard\Git\GitDiffProvider $gitDiffProvider)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->gitDiffProvider = $gitDiffProvider;
        $this->fileExtensions = $parameterProvider->provideArrayParameter(\Symplify\EasyCodingStandard\ValueObject\Option::FILE_EXTENSIONS);
    }
    /**
     * @param string[] $source
     * @param bool $doesMatchGitDiff - @deprecated
     * @return SmartFileInfo[]
     */
    public function find(array $source, bool $doesMatchGitDiff = \false) : array
    {
        $fileInfos = [];
        foreach ($source as $singleSource) {
            if (\is_file($singleSource)) {
                $fileInfos[] = new \ECSPrefix20211002\Symplify\SmartFileSystem\SmartFileInfo($singleSource);
            } else {
                $filesInDirectory = $this->processDirectory($singleSource);
                $fileInfos = \array_merge($fileInfos, $filesInDirectory);
            }
        }
        $fileInfos = $this->filterOutGitDiffFiles($fileInfos, $doesMatchGitDiff);
        \ksort($fileInfos);
        return $fileInfos;
    }
    /**
     * @return SmartFileInfo[]
     */
    private function processDirectory(string $directory) : array
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);
        $finder = \ECSPrefix20211002\Symfony\Component\Finder\Finder::create()->files()->name($normalizedFileExtensions)->in($directory)->exclude('vendor')->size('> 0')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
    /**
     * @param string[] $fileExtensions
     * @return string[]
     */
    private function normalizeFileExtensions(array $fileExtensions) : array
    {
        $normalizedFileExtensions = [];
        foreach ($fileExtensions as $fileExtension) {
            $normalizedFileExtensions[] = '*.' . $fileExtension;
        }
        return $normalizedFileExtensions;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @param bool $doesMatchGitDiff @deprecated
     * @return SmartFileInfo[]
     */
    private function filterOutGitDiffFiles(array $fileInfos, bool $doesMatchGitDiff) : array
    {
        if (!$doesMatchGitDiff) {
            return $fileInfos;
        }
        $gitDiffFiles = $this->gitDiffProvider->provide();
        $fileInfos = \array_filter($fileInfos, function ($splFile) use($gitDiffFiles) : bool {
            return \in_array($splFile->getRealPath(), $gitDiffFiles, \true);
        });
        return \array_values($fileInfos);
    }
}
