<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\SmartFileInfo;

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
     * @param string[] $fileExtensions
     */
    public function __construct(FinderSanitizer $finderSanitizer, array $fileExtensions)
    {
        $this->finderSanitizer = $finderSanitizer;
        $this->fileExtensions = $fileExtensions;
    }

    /**
     * @param string[] $source
     * @return SmartFileInfo[]
     */
    public function find(array $source): array
    {
        $files = [];
        foreach ($source as $singleSource) {
            if (is_file($singleSource)) {
                $files[] = new SmartFileInfo($singleSource);
            } else {
                $filesInDirectory = $this->processDirectory($singleSource);
                $files = array_merge($files, $filesInDirectory);
            }
        }

        ksort($files);

        return $files;
    }

    /**
     * @return SmartFileInfo[]
     */
    private function processDirectory(string $directory): array
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);

        $finder = Finder::create()->files()
            ->name($normalizedFileExtensions)
            ->in($directory)
            ->exclude('vendor')
            ->sortByName();

        return $this->finderSanitizer->sanitize($finder);
    }

    /**
     * @param string[] $fileExtensions
     * @return string[]
     */
    private function normalizeFileExtensions(array $fileExtensions): array
    {
        $normalizedFileExtensions = [];

        foreach ($fileExtensions as $fileExtension) {
            $normalizedFileExtensions[] = '*.' . $fileExtension;
        }

        return $normalizedFileExtensions;
    }
}
