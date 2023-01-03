<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;

final class SingleFileProcessor
{
    public function __construct(
        private readonly Skipper $skipper,
        private readonly ChangedFilesDetector $changedFilesDetector,
        private readonly FileProcessorCollector $fileProcessorCollector
    ) {
    }

    /**
     * @return array{file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[]}
     */
    public function processFileInfo(SplFileInfo $fileInfo, Configuration $configuration): array
    {
        if ($this->skipper->shouldSkipFileInfo($fileInfo)) {
            return [];
        }

        $errorsAndDiffs = [];

        $this->changedFilesDetector->addFileInfo($fileInfo);
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();

        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers() === []) {
                continue;
            }

            $currentErrorsAndFileDiffs = $fileProcessor->processFile($fileInfo, $configuration);
            if ($currentErrorsAndFileDiffs === []) {
                continue;
            }

            $errorsAndDiffs = array_merge($errorsAndDiffs, $currentErrorsAndFileDiffs);
        }

        // invalidate broken file, to analyse in next run too
        if ($errorsAndDiffs !== []) {
            $this->changedFilesDetector->invalidateFileInfo($fileInfo);
        }

        return $errorsAndDiffs;
    }
}
