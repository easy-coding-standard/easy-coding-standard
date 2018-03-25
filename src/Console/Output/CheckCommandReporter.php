<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Console\Output;

use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\FileDiff;
use Symplify\EasyCodingStandard\Performance\CheckerMetricRecorder;
use Symplify\EasyCodingStandard\Skipper;

final class CheckCommandReporter
{
    /**
     * @var EasyCodingStandardStyle
     */
    private $easyCodingStandardStyle;

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
        EasyCodingStandardStyle $easyCodingStandardStyle,
        CheckerMetricRecorder $checkerMetricRecorder,
        Skipper $skipper,
        Configuration $configuration
    ) {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->checkerMetricRecorder = $checkerMetricRecorder;
        $this->skipper = $skipper;
        $this->configuration = $configuration;
    }

    public function reportPerformance(): void
    {
        if (! $this->configuration->showPerformance()) {
            return;
        }

        $this->easyCodingStandardStyle->newLine();
        $this->easyCodingStandardStyle->title('Performance Statistics');

        $this->easyCodingStandardStyle->printMetrics($this->checkerMetricRecorder->getMetrics());
    }

    public function reportUnusedSkipped(): void
    {
        foreach ($this->skipper->getUnusedSkipped() as $skippedClass => $skippedFiles) {
            foreach ($skippedFiles as $skippedFile) {
                if (! $this->isFileInSource($skippedFile)) {
                    continue;
                }

                $this->easyCodingStandardStyle->error(sprintf(
                    'Skipped checker "%s" and file path "%s" were not found. '
                    . 'You can remove them from "parameters: > skip:" section in your config.',
                    $skippedClass,
                    $skippedFile
                ));
            }
        }
    }

    /**
     * @param FileDiff[][] $fileDiffPerFile
     */
    public function reportFileDiffs(array $fileDiffPerFile): void
    {
        if (! count($fileDiffPerFile)) {
            return;
        }

        $this->easyCodingStandardStyle->newLine();

        $i = 0;
        foreach ($fileDiffPerFile as $file => $fileDiffs) {
            $this->easyCodingStandardStyle->newLine(2);
            $this->easyCodingStandardStyle->writeln(sprintf('<options=bold>%d) %s</>', ++$i, $file));

            foreach ($fileDiffs as $fileDiff) {
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->writeln($fileDiff->getDiffConsoleFormatted());
                $this->easyCodingStandardStyle->newLine();

                $this->easyCodingStandardStyle->writeln('Applied checkers:');
                $this->easyCodingStandardStyle->newLine();
                $this->easyCodingStandardStyle->listing($fileDiff->getAppliedCheckers());
            }
        }
    }

    private function isFileInSource(string $file): bool
    {
        foreach ($this->configuration->getSources() as $source) {
            if (fnmatch('**' . $source . '**', $file)) {
                return true;
            }
        }

        return false;
    }
}
