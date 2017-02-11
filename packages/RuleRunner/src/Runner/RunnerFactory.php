<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\RuleRunner\Runner;

use PhpCsFixer\Cache\CacheManagerInterface;
use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Error\ErrorsManager;
use PhpCsFixer\Finder;
use PhpCsFixer\Linter\LinterInterface;
use PhpCsFixer\Runner\Runner;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symplify\EasyCodingStandard\RuleRunner\Fixer\FixerFactory;

final class RunnerFactory
{
    /**
     * @var DifferInterface
     */
    private $differ;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var ErrorsManager
     */
    private $errorsManager;

    /**
     * @var LinterInterface
     */
    private $linter;

    /**
     * @var CacheManagerInterface
     */
    private $cacheManager;

    /**
     * @var FixerFactory
     */
    private $fixerFactory;

    public function __construct(
        DifferInterface $differ,
        EventDispatcherInterface $eventDispatcher,
        ErrorsManager $errorsManager,
        LinterInterface $linter,
        CacheManagerInterface $cacheManager,
        FixerFactory $fixerFactory
    ) {
        $this->differ = $differ;
        $this->eventDispatcher = $eventDispatcher;
        $this->errorsManager = $errorsManager;
        $this->linter = $linter;
        $this->cacheManager = $cacheManager;
        $this->fixerFactory = $fixerFactory;
    }

    public function create(array $rules, array $excludedRules, string $source, bool $isFixer) : Runner
    {
        $fixers = $this->fixerFactory->createFromRulesAndExcludedRules($rules, $excludedRules);

        return new Runner(
            $this->createFinderForSource($source),
            $fixers,
            $this->differ,
            $this->eventDispatcher,
            $this->errorsManager,
            $this->linter,
            ! $isFixer,
            $this->cacheManager
        );
    }

    private function createFinderForSource(string $source): Finder
    {
        return (new Finder)->files()
            ->in($source)
            ->name('*.php');
    }
}
