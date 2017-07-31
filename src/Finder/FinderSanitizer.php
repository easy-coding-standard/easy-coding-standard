<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SplFileInfo[]|SymfonyFinder $finder
     * @return SplFileInfo[]
     */
    public function sanitize($finder): array
    {
        $finder = $this->filterOutEmptyFiles($finder);

        return $this->turnToSplFilesIfFinder($finder);
    }

    /**
     * @param NetteFinder|SplFileInfo[]|SymfonyFinder $finder
     * @return SplFileInfo[]
     */
    private function turnToSplFilesIfFinder($finder): array
    {
        if (! $finder instanceof NetteFinder && ! $finder instanceof SymfonyFinder) {
            return $finder;
        }

        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param NetteFinder|SplFileInfo[]|SymfonyFinder $finder
     * @return NetteFinder|SplFileInfo[]|SymfonyFinder
     */
    private function filterOutEmptyFiles($finder)
    {
        if ($finder instanceof SymfonyFinder) {
            $finder->size('> 0');

            return $finder;
        }

        if ($finder instanceof NetteFinder) {
            $finder->size('> 0');

            return $finder;
        }

        return array_filter($finder, 'filesize');
    }
}
