<?php declare(strict_types=1);

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
                $files = $this->processFile($files, $singleSource);
            } else {
                $files = $this->processDirectory($files, $singleSource);
            }
        }

        ksort($files);

        return $files;
    }

    /**
     * @param SmartFileInfo[] $files
     * @return SmartFileInfo[]
     */
    private function processFile(array $files, string $file): array
    {
        return array_merge($files, [new SmartFileInfo($file)]);
    }

    /**
     * @param SmartFileInfo[] $files
     * @return SmartFileInfo[]
     */
    private function processDirectory(array $files, string $directory): array
    {
        $normalizedFileExtensions = $this->normalizeFileExtensions($this->fileExtensions);

        $finder = Finder::create()->files()
            ->name($normalizedFileExtensions)
            ->in($directory)
            ->exclude('vendor')
            ->sortByName();

        $newFiles = $this->finderSanitizer->sanitize($finder);

        return array_merge($files, $newFiles);
    }

    /**
     * @param string[] $fileExtensions
     * @return string[]
     */
    private function normalizeFileExtensions(array $fileExtensions)
    {
        $normalizedFileExtensions = [];

        foreach ($fileExtensions as $fileExtension) {
            $normalizedFileExtensions[] = '*.' . $fileExtension;
        }

        return $normalizedFileExtensions;
    }
}
