<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Finder;

use ECSPrefix202208\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202208\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202208\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(FinderSanitizer $finderSanitizer, ParameterProvider $parameterProvider)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileExtensions = $parameterProvider->provideArrayParameter(Option::FILE_EXTENSIONS);
    }
    /**
     * @param string[] $source
     * @return SmartFileInfo[]
     */
    public function find(array $source) : array
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
        \ksort($fileInfos);
        return $fileInfos;
    }
    /**
     * @return SmartFileInfo[]
     */
    private function processDirectory(string $directory) : array
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);
        $finder = Finder::create()->files()->name($normalizedFileExtensions)->in($directory)->exclude('vendor')->size('> 0')->sortByName();
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
}
