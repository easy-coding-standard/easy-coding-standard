<?php

namespace Symplify\EasyCodingStandard\Finder;

use ECSPrefix20210507\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Git\GitDiffProvider;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Parameter\ParameterProvider;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;
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
     * @var FinderSanitizer
     */
    private $finderSanitizer;
    /**
     * @var GitDiffProvider
     */
    private $gitDiffProvider;
    /**
     * @param \Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer
     * @param \Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider
     * @param \Symplify\EasyCodingStandard\Git\GitDiffProvider $gitDiffProvider
     */
    public function __construct($finderSanitizer, $parameterProvider, $gitDiffProvider)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileExtensions = $parameterProvider->provideArrayParameter(Option::FILE_EXTENSIONS);
        $this->gitDiffProvider = $gitDiffProvider;
    }
    /**
     * @param string[] $source
     * @return mixed[]
     * @param bool $doesMatchGitDiff
     */
    public function find(array $source, $doesMatchGitDiff = \false)
    {
        $fileInfos = [];
        foreach ($source as $singleSource) {
            if (\is_file($singleSource)) {
                $fileInfos[] = new SmartFileInfo($singleSource);
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
     * @return mixed[]
     * @param string $directory
     */
    private function processDirectory($directory)
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);
        $finder = Finder::create()->files()->name($normalizedFileExtensions)->in($directory)->exclude('vendor')->size('> 0')->sortByName();
        return $this->finderSanitizer->sanitize($finder);
    }
    /**
     * @param string[] $fileExtensions
     * @return mixed[]
     */
    private function normalizeFileExtensions(array $fileExtensions)
    {
        $normalizedFileExtensions = [];
        foreach ($fileExtensions as $fileExtension) {
            $normalizedFileExtensions[] = '*.' . $fileExtension;
        }
        return $normalizedFileExtensions;
    }
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return mixed[]
     * @param bool $doesMatchGitDiff
     */
    private function filterOutGitDiffFiles(array $fileInfos, $doesMatchGitDiff)
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
