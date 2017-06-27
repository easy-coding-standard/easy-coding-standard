<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

final class SourceFinder
{
    /**
     * @param string[]
     * @return SplFileInfo[]
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
     * @param string $file
     * @return SplFileInfo[]
     */
    private function processDirectory(array $files, string $directory): array
    {
        $finder = (new Finder)->files()
            ->name('*.php')
            ->in($directory);

        return array_merge($files, iterator_to_array($finder->getIterator()));
    }
}
