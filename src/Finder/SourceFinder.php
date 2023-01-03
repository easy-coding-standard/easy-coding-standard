<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;
use ECSPrefix202301\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\ValueObject\Option;
use ECSPrefix202301\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202301\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\EasyCodingStandard\Tests\Finder\SourceFinderTest
 */
final class SourceFinder
{
    /**
     * @var string[]
     */
    private $fileExtensions = [];
    public function __construct(ParameterProvider $parameterProvider)
    {
        $this->fileExtensions = $parameterProvider->provideArrayParameter(Option::FILE_EXTENSIONS);
    }
    /**
     * @param string[] $source
     * @return SplFileInfo[]
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
     * @return SplFileInfo[]
     */
    private function processDirectory(string $directory) : array
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);
        $finder = Finder::create()->files()->name($normalizedFileExtensions)->in($directory)->exclude('vendor')->size('> 0')->sortByName();
        return \iterator_to_array($finder);
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
