<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Performance;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Symplify\EasyCodingStandard\Configuration\Configuration;

final class CheckerMetricRecorder
{
    /**
     * @var int
     */
    private const DISPLAY_LIMIT = 20;

    /**
     * @var Stopwatch
     */
    private $stopwatch;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Stopwatch $stopwatch, Configuration $configuration)
    {
        $this->stopwatch = $stopwatch;
        $this->configuration = $configuration;
    }

    /**
     * @param Sniff|FixerInterface $checker
     */
    public function startWithChecker($checker): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $this->stopwatch->start(get_class($checker));
    }

    /**
     * @param Sniff|FixerInterface $checker
     */
    public function endWithChecker($checker): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $checkerClass = get_class($checker);
        if (! $this->stopwatch->isStarted($checkerClass)) {
            return;
        }

        $this->stopwatch->stop($checkerClass);
    }

    /**
     * @return int[]
     */
    public function getMetrics(): array
    {
        $checkerWithDuration = [];

        foreach ($this->stopwatch->getSectionEvents('__root__') as $checkerClass => $sectionEvent) {
            $checkerWithDuration[$checkerClass] = $sectionEvent->getDuration();
        }

        arsort($checkerWithDuration);

        array_splice($checkerWithDuration, self::DISPLAY_LIMIT);

        return $checkerWithDuration;
    }
}
