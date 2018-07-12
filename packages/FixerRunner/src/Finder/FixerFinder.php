<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Finder;

final class FixerFinder
{
    /**
     * @var string[][]
     */
    private $sniffClassesPerDirectory = [];

    /**
     * @var FixerClassRobotLoaderFactory
     */
    private $sniffClassRobotLoaderFactory;

    /**
     * @var FixerClassFilter
     */
    private $sniffClassFilter;

    public function __construct(
        FixerClassRobotLoaderFactory $sniffClassRobotLoaderFactory,
        FixerClassFilter $sniffClassFilter
    ) {
        $this->sniffClassRobotLoaderFactory = $sniffClassRobotLoaderFactory;
        $this->sniffClassFilter = $sniffClassFilter;
    }

    /**
     * @return string[]
     */
    public function findAllFixerClassesInDirectory(string $directory): array
    {
        if (isset($this->sniffClassesPerDirectory[$directory])) {
            return $this->sniffClassesPerDirectory[$directory];
        }

        $robotLoader = $this->sniffClassRobotLoaderFactory->createForDirectory($directory);
        $foundFixerClasses = array_keys($robotLoader->getIndexedClasses());
        $sniffClasses = $this->sniffClassFilter->filterOutAbstractAndNonPhpFixerClasses($foundFixerClasses);

        return $this->sniffClassesPerDirectory[$directory] = $sniffClasses;
    }
}
