<?php
declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Guard;

use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;

final class LoadedCheckersGuard
{
    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;

    public function __construct(FileProcessorCollector $fileProcessorCollector)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
    }

    public function ensureSomeCheckersAreRegistered(): void
    {
        $checkerCount = $this->getCheckerCount();
        if ($checkerCount !== 0) {
            return;
        }

        throw new NoCheckersLoadedException();
    }

    private function getCheckerCount(): int
    {
        $checkerCount = 0;

        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += count($fileProcessor->getCheckers());
        }

        return $checkerCount;
    }
}
