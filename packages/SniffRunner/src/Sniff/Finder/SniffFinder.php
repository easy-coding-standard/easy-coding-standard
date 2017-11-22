<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Sniff\Finder;

final class SniffFinder
{
    /**
     * @var string[][]
     */
    private $sniffClassesPerDirectory = [];

    /**
     * @var SniffClassRobotLoaderFactory
     */
    private $sniffClassRobotLoaderFactory;

    /**
     * @var SniffClassFilter
     */
    private $sniffClassFilter;

    public function __construct(
        SniffClassRobotLoaderFactory $sniffClassRobotLoaderFactory,
        SniffClassFilter $sniffClassFilter
    ) {
        $this->sniffClassRobotLoaderFactory = $sniffClassRobotLoaderFactory;
        $this->sniffClassFilter = $sniffClassFilter;
    }

    /**
     * @return string[]
     */
    public function findAllSniffClassesInDirectory(string $directory): array
    {
        if (isset($this->sniffClassesPerDirectory[$directory])) {
            return $this->sniffClassesPerDirectory[$directory];
        }

        $robotLoader = $this->sniffClassRobotLoaderFactory->createForDirectory($directory);
        $foundSniffClasses = array_keys($robotLoader->getIndexedClasses());
        $sniffClasses = $this->sniffClassFilter->filterOutAbstractAndNonPhpSniffClasses($foundSniffClasses);

        return $this->sniffClassesPerDirectory[$directory] = $sniffClasses;
    }
}
