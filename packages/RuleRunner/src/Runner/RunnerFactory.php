<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Finder;
use Symplify\EasyCodingStandard\RuleRunner\Fixer\FixerFactory;

final class RunnerFactory
{
    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    public function __construct(DifferInterface $differ, FixerFactory $fixerFactory)
    {
        $this->differ = $differ;
        $this->fixerFactory = $fixerFactory;
    }

    public function create(array $enabledRules, array $excludedRules, string $source, bool $isFixer) : Runner
    {
        $fixers = $this->fixerFactory->createFromEnabledAndExcludedRules($enabledRules, $excludedRules);

        return new Runner(
            $this->createFinderForSource($source),
            $fixers,
            $this->differ,
            ! $isFixer
        );
    }

    private function createFinderForSource(string $source) : Finder
    {
        return (new Finder)->files()
            ->in($source)
            ->name('*.php');
    }
}
