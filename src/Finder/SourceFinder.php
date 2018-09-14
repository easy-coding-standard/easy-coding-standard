<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use function Safe\ksort;

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
     * @return SymfonySplFileInfo[]
     */
    public function find(array $source): array
    {
        if ($this->customSourceProvider) {
            $finder = $this->customSourceProvider->find($source);

            return $this->finderSanitizer->sanitize($finder);
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
     * @param SplFileInfo[] $files
     * @return SplFileInfo[]
     */
    private function processFile(array $files, string $file): array
    {
        $fileInfo = new SplFileInfo($file);

        return array_merge($files, [
            $file => new SymfonySplFileInfo($file, dirname($fileInfo->getFilename()), $fileInfo->getFilename()),
        ]);
    }

    /**
     * @param SplFileInfo[] $files
     * @return SplFileInfo[]
     */
    private function processDirectory(array $files, string $directory): array
    {
        $finder = Finder::create()->files()
            ->name('*.php')
            ->in($directory);

        $newFiles = $this->finderSanitizer->sanitize($finder);

        return array_merge($files, $newFiles);
    }
}
