<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Runner;

use PhpCsFixer\Finder;
use SplFileInfo;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\FixerRunner\Fixer\FixerFactory;

final class RunnerFactory
{
    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    /**
     * @var ErrorDataCollector
     */
    private $errorDataCollector;

    public function __construct(FixerFactory $fixerFactory, ErrorDataCollector $errorDataCollector)
    {
        $this->fixerFactory = $fixerFactory;
        $this->errorDataCollector = $errorDataCollector;
    }

    public function create(array $fixerClasses, string $source, bool $isFixer) : Runner
    {
        return new Runner(
            $this->findFilesInSource($source),
            $isFixer,
            $this->fixerFactory->createFromClasses($fixerClasses),
            $this->errorDataCollector
        );
    }

    private function findFilesInSource(string $source) : array
    {
        if (is_file($source)) {
            return [
                $source => new SplFileInfo($source)
            ];
        }

        return (new Finder)->files()
            ->in($source)
            ->name('*.php')
            ->getIterator();

    }
}
