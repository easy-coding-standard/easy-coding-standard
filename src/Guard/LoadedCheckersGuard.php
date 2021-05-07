<?php

namespace Symplify\EasyCodingStandard\Guard;

use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
final class LoadedCheckersGuard
{
    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @param \Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector
     */
    public function __construct($fileProcessorCollector)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
    }
    /**
     * @return void
     */
    public function ensureSomeCheckersAreRegistered()
    {
        $checkerCount = $this->getCheckerCount();
        if ($checkerCount !== 0) {
            return;
        }
        throw new NoCheckersLoadedException();
    }
    /**
     * @return int
     */
    private function getCheckerCount()
    {
        $checkerCount = 0;
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += \count($fileProcessor->getCheckers());
        }
        return $checkerCount;
    }
}
