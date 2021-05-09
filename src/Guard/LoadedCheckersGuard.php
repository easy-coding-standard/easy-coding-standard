<?php

namespace Symplify\EasyCodingStandard\Guard;

use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
final class LoadedCheckersGuard
{
    /**
     * @var FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @var NoCheckersLoaderReporter
     */
    private $noCheckersLoaderReporter;
    public function __construct(\Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector, \Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter $noCheckersLoaderReporter)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
        $this->noCheckersLoaderReporter = $noCheckersLoaderReporter;
    }
    /**
     * @return bool
     */
    public function areSomeCheckerRegistered()
    {
        $checkerCount = $this->getCheckerCount();
        return $checkerCount !== 0;
    }
    /**
     * @return void
     */
    public function report()
    {
        $this->noCheckersLoaderReporter->report();
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
