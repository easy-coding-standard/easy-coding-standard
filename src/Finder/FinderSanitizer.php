<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use Nette\Utils\Finder as NetteFinder;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use Symplify\EasyCodingStandard\Exception\Finder\InvalidSourceTypeException;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SplFileInfo[]|SymfonyFinder $finder
     * @return SplFileInfo[]
     */
    public function sanitize($finder): array
    {
        $this->ensureIsFinder($finder);

        $finder = $this->filterOutEmptyFiles($finder);

        return $this->turnToSplFiles($finder);
    }

    /**
     * @param NetteFinder|SymfonyFinder $finder
     * @return SplFileInfo[]
     */
    private function turnToSplFiles($finder): array
    {
        return iterator_to_array($finder->getIterator());
    }

    /**
     * @param NetteFinder|SymfonyFinder $finder
     * @return NetteFinder|SymfonyFinder
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
    }

    /**
     * @param mixed $finder
     */
    private function ensureIsFinder($finder): void
    {
        if ($finder instanceof NetteFinder || $finder instanceof SymfonyFinder) {
            return;
        }

        $sourceType = is_object($finder) ? get_class($finder) :
            is_array($finder) ? gettype($finder) : $finder;

        throw new InvalidSourceTypeException(sprintf(
            '%s is not valid source type, probably in your %s class. Return "%s" or "%s"',
            $sourceType,
            CustomSourceProviderInterface::class,
            NetteFinder::class,
            SymfonyFinder::class
        ));
    }
}
