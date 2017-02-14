<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\Finder;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\EasyCodingStandard\RuleRunner\Fixer\FixerFactory;

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

    public function create(array $enabledRules, array $excludedRules, string $source, bool $isFixer) : Runner
    {
        $fixers = $this->fixerFactory->createFromEnabledAndExcludedRules($enabledRules, $excludedRules);

        return new Runner(
            $this->createFinderForSource($source),
            ! $isFixer,
            $fixers,
            $this->errorDataCollector
        );
    }

    private function createFinderForSource(string $source) : Finder
    {
        return (new Finder)->files()
            ->in($source)
            ->name('*.php');
    }
}
