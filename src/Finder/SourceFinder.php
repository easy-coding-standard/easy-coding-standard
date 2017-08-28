<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;

final class SourceFinder
{
    /**
     * @var null|CustomSourceProviderInterface
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
     * @param string[]
     * @return SplFileInfo[]
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

        return $files;
    }

    /**
     * @param SplFileInfo[] $files
     * @return SplFileInfo[]
     */
    private function processFile(array $files, string $file): array
    {
        return array_merge($files, [
            $file => new SplFileInfo($file),
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
