<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use Symplify\PackageBuilder\FileSystem\FinderSanitizer;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SourceFinder
{
    /**
     * @var CustomSourceProviderInterface|null
     */
    private $customSourceProvider;

    /**
     * @var FinderSanitizer
     */
    private $finderSanitizer;

    public function __construct(FinderSanitizer $finderSanitizer)
    {
        $this->finderSanitizer = $finderSanitizer;
    }

    public function setCustomSourceProvider(CustomSourceProviderInterface $customSourceProvider): void
    {
        $this->customSourceProvider = $customSourceProvider;
    }

    /**
     * @param string[] $source
     * @return SmartFileInfo[]
     */
    public function find(array $source): array
    {
        if ($this->customSourceProvider) {
            $files = $this->customSourceProvider->find($source);

            return $this->finderSanitizer->sanitize($files);
        }

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
        $finder = Finder::create()->files()
            ->name('*.php')
            ->in($directory)
            ->exclude('vendor')
            ->sortByName();

        $newFiles = $this->finderSanitizer->sanitize($finder);

        return array_merge($files, $newFiles);
    }
}
