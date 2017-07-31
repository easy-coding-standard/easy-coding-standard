<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[] $finder
     * @return SplFileInfo[]
     */
    public function sanitize($finder): array
    {
        if ($finder instanceof SymfonyFinder) {
            $finder->size('> 0');
        } elseif ($finder instanceof NetteFinder) {
            $finder->size('>=', 1);
        }

        return $this->turnToSplFilesIfFinder($finder);
    }

    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[] $finder
     * @return SplFileInfo[]
     */
    private function turnToSplFilesIfFinder($finder): array
    {
        if (! $finder instanceof NetteFinder && ! $finder instanceof SymfonyFinder) {
            return $finder;
        }

        return iterator_to_array($finder->getIterator());
    }
}
