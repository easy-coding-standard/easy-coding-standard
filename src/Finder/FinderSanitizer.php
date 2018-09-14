<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Finder;

use IteratorAggregate;
use Nette\Utils\Finder as NetteFinder;
use Nette\Utils\Strings;
use ReflectionClass;
use SplFileInfo;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\EasyCodingStandard\Exception\Finder\InvalidSourceTypeException;
use function Safe\sprintf;

final class FinderSanitizer
{
    /**
     * @param NetteFinder|SymfonyFinder|SplFileInfo[] $finder
     * @return SymfonySplFileInfo[]
     */
    public function sanitize($finder): array
    {
        $fileInfos = $this->turnToFileInfos($finder);

        $fileInfos = $this->filterOutEmptyFiles($fileInfos);

        return $this->turnInfoSymfonyFileInfos($fileInfos, $finder);
    }

    /**
     * @param SplFileInfo[] $fileInfos
     * @return SplFileInfo[]
     */
    private function filterOutEmptyFiles(array $fileInfos): array
    {
        return array_filter($fileInfos, function (SplFileInfo $fileInfo) {
            $this->ensureFileInfoExists($fileInfo);

            return filesize($fileInfo->getRealPath());
        });
    }

    /**
     * Used logic from @see \Symfony\Component\Finder\Iterator\RecursiveDirectoryIterator::current()
     *
     * @param SplFileInfo[]|SymfonySplFileInfo[] $fileInfos
     * @param SymfonyFinder|NetteFinder $finder
     * @return SymfonySplFileInfo[]
     */
    private function turnInfoSymfonyFileInfos(array $fileInfos, $finder): array
    {
        if ($finder instanceof SymfonyFinder || ! $finder instanceof NetteFinder) {
            return $fileInfos;
        }

        /** @var NetteFinder $finder */
        $paths = $this->getPrivateProperty($finder, 'paths');

        foreach ($fileInfos as $key => $fileInfo) {
            $relativePathname = $this->resolveRelativePath($paths, $fileInfo);
            $fileInfos[$key] = new SymfonySplFileInfo(
                $fileInfo->getPathname(),
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
                return Strings::substring($fileInfo->getRealPath(), strlen($path) + 1);
            }
        }

        // dump fallback
        return $fileInfo->getFilename();
    }

    /**
     * @param SplFileInfo[]|NetteFinder|SymfonyFinder $iterableFileInfos
     * @return SplFileInfo[]
     */
    private function turnToFileInfos(iterable $iterableFileInfos): array
    {
        if ($iterableFileInfos instanceof IteratorAggregate) {
            return iterator_to_array($iterableFileInfos);
        }

        return $iterableFileInfos;
    }

    private function ensureFileInfoExists(SplFileInfo $fileInfo): void
    {
        if (file_exists($fileInfo->getPath()) && $fileInfo->getRealPath()) {
            return;
        }

        throw new InvalidSourceTypeException(sprintf('%s file does not exist', $fileInfo->getPath()));
    }
}
