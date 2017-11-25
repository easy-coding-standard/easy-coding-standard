<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;

final class CheckCommandReporter
{
    /**
     * @var SymfonyStyle
     */
    private $symfonyStyle;

    /**
     * @var CheckerMetricRecorder
     */
    private $checkerMetricRecorder;

    public function __construct(SymfonyStyle $symfonyStyle, CheckerMetricRecorder $checkerMetricRecorder)
    {
        $this->symfonyStyle = $symfonyStyle;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
    }

    public function reportPerformance(): void
    {
        $this->symfonyStyle->newLine();

        $this->symfonyStyle->title('Performance Statistics');

        $metrics = $this->checkerMetricRecorder->getMetrics();
        $metricsForTable = $this->prepareMetricsForTable($metrics);
        $this->symfonyStyle->table(['Checker', 'Total duration'], $metricsForTable);
    }

    /**
     * @param mixed[] $metrics
     * @return mixed[]
     */
    private function prepareMetricsForTable(array $metrics): array
    {
        $metricsForTable = [];
        foreach ($metrics as $checkerClass => $duration) {
            $metricsForTable[] = [$checkerClass, $duration . ' ms'];
        }

        return $metricsForTable;
    }
}
