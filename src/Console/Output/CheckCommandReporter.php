<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symfony\Component\Console\Style\SymfonyStyle;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;

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

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        SymfonyStyle $symfonyStyle,
        CheckerMetricRecorder $checkerMetricRecorder,
        Skipper $skipper,
        Configuration $configuration
    ) {
        $this->symfonyStyle = $symfonyStyle;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
    }

    public function reportPerformance(): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $this->symfonyStyle->newLine();

        $this->symfonyStyle->title('Performance Statistics');

        $metrics = $this->checkerMetricRecorder->getMetrics();
        $metricsForTable = $this->prepareMetricsForTable($metrics);
        $this->symfonyStyle->table(['Checker', 'Total duration'], $metricsForTable);
    }

    public function reportUnusedSkipped(): void
    {
        foreach ($this->skipper->getUnusedSkipped() as $skippedClass => $skippedFiles) {
            foreach ($skippedFiles as $skippedFile) {
                if (! $this->isSkippedFileInSource($skippedFile)) {
                    continue;
                }

                $this->symfonyStyle->error(sprintf(
                    'Skipped checker "%s" and file path "%s" were not found. '
                    . 'You can remove them from "parameters: > skip:" section in your config.',
                    $skippedClass,
                    $skippedFile
                ));
            }
        }
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

    private function isSkippedFileInSource(string $skippedFile): bool
    {
        foreach ($this->configuration->getSources() as $source) {
            if (fnmatch(sprintf('*%s', $source), $skippedFile)) {
                return true;
            }
        }

        return false;
    }
}
