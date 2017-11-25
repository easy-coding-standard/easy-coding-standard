<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use IteratorAggregate;
use Nette\Utils\Finder as NetteFinder;
use Nette\Utils\Strings;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\EasyCodingStandard\Contract\Finder\CustomSourceProviderInterface;
use Symplify\EasyCodingStandard\Exception\Finder\InvalidSourceTypeException;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SymfonyFinder $finder
     * @return SymfonySplFileInfo[]
     */
    public function sanitize($finder): array
    {
        $this->ensureIsFinder($finder);

        $fileInfos = $this->turnFinderToFileInfos($finder);

        $fileInfos = $this->filterOutEmptyFiles($fileInfos);

        return $this->turnInfoSymfonyFileInfos($fileInfos, $finder);
    }

    /**
     * @return SplFileInfo[]
     */
    private function turnFinderToFileInfos(IteratorAggregate $finder): array
    {
        return iterator_to_array($finder);
    }

    /**
     * @param SplFileInfo[] $splFiles
     * @return SplFileInfo[]
     */
    private function filterOutEmptyFiles(array $splFiles): array
    {
        return array_filter($splFiles, 'filesize');
    }

    /**
     * @param mixed $finder
     */
    private function ensureIsFinder($finder): void
    {
        if ($finder instanceof NetteFinder || $finder instanceof SymfonyFinder) {
            return;
        }

        $sourceType = is_object($finder) ? get_class($finder) : (is_array($finder) ? gettype($finder) : $finder);

        throw new InvalidSourceTypeException(sprintf(
            '%s is not valid source type, probably in your %s class in "find()" method. Return "%s" or "%s".',
            $sourceType,
            CustomSourceProviderInterface::class,
            NetteFinder::class,
            SymfonyFinder::class
        ));
    }

    /**
     * Used logic from @see \Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator::current()
     *
     * @param SplFileInfo[] $fileInfos
     * @param SymfonyFinder|NetteFinder $finder
     * @return SymfonySplFileInfo[]
     */
    private function turnInfoSymfonyFileInfos(array $fileInfos, $finder): array
    {
        if ($finder instanceof SymfonyFinder) {
            return $fileInfos;
        }

        /** @var NetteFinder $finder */
        $paths = $this->getPrivateProperty($finder, 'paths');

        foreach ($fileInfos as $key => $fileInfo) {
            $relativePathname = $this->resolveRelativePath($paths, $fileInfo);
            $fileInfos[$key] = new SymfonySplFileInfo(
                $fileInfo->getFilename(),
                dirname($relativePathname),
                $relativePathname
            );
        }

        return $fileInfos;
    }

    /**
     * @param object $object
     * @return mixed
     */
    private function getPrivateProperty($object, string $property)
    {
        $reflectionClass = new ReflectionClass($object);
        $propertyReflection = $reflectionClass->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($object);
    }

    /**
     * @param string[] $paths
     */
    private function resolveRelativePath(array $paths, SplFileInfo $fileInfo): string
    {
        foreach ($paths as $path) {
            if (Strings::startsWith($fileInfo->getRealPath(), $path)) {
                return substr($fileInfo->getRealPath(), strlen($path) + 1);
            }
        }

        // dump fallback
        return $fileInfo->getFilename();
    }
}
