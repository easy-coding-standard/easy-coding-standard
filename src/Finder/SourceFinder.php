<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Finder;

use ECSPrefix20220220\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix20220220\Symplify\SmartFileSystem\Finder\FinderSanitizer;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\ECSPrefix20220220\Symplify\SmartFileSystem\Finder\FinderSanitizer $finderSanitizer, \ECSPrefix20220220\Symplify\PackageBuilder\Parameter\ParameterProvider $parameterProvider)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileExtensions = $parameterProvider->provideArrayParameter(\Symplify\EasyCodingStandard\ValueObject\Option::FILE_EXTENSIONS);
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
                $fileInfos[] = new \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo($singleSource);
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
        $finder = \ECSPrefix20220220\Symfony\Component\Finder\Finder::create()->files()->name($normalizedFileExtensions)->in($directory)->exclude('vendor')->size('> 0')->sortByName();
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
