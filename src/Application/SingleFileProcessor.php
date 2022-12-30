<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Skipper\Skipper\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SingleFileProcessor
{
    public function __construct(
        private Skipper $skipper,
        private ChangedFilesDetector $changedFilesDetector,
        private FileProcessorCollector $fileProcessorCollector
    ) {
    }

    /**
     * @return array{file_diffs?: FileDiff[], coding_standard_errors?: CodingStandardError[]}
     */
    public function processFileInfo(SmartFileInfo $smartFileInfo, Configuration $configuration): array
    {
        if ($this->skipper->shouldSkipFileInfo($smartFileInfo)) {
            return [];
        }

        $errorsAndDiffs = [];

        $this->changedFilesDetector->addFileInfo($smartFileInfo);
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();

        foreach ($fileProcessors as $fileProcessor) {
            if ($fileProcessor->getCheckers() === []) {
                continue;
            }

            $currentErrorsAndFileDiffs = $fileProcessor->processFile($smartFileInfo, $configuration);
            if ($currentErrorsAndFileDiffs === []) {
                continue;
            }

            $errorsAndDiffs = array_merge($errorsAndDiffs, $currentErrorsAndFileDiffs);
        }

        // invalidate broken file, to analyse in next run too
        if ($errorsAndDiffs !== []) {
            $this->changedFilesDetector->invalidateFileInfo($smartFileInfo);
        }

        return $errorsAndDiffs;
    }
}
