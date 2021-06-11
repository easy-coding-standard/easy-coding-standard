<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Guard;

use Symplify\EasyCodingStandard\Application\FileProcessorCollector;
use Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter;
final class LoadedCheckersGuard
{
    /**
     * @var \Symplify\EasyCodingStandard\Application\FileProcessorCollector
     */
    private $fileProcessorCollector;
    /**
     * @var \Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter
     */
    private $noCheckersLoaderReporter;
    public function __construct(\Symplify\EasyCodingStandard\Application\FileProcessorCollector $fileProcessorCollector, \Symplify\EasyCodingStandard\Bootstrap\NoCheckersLoaderReporter $noCheckersLoaderReporter)
    {
        $this->fileProcessorCollector = $fileProcessorCollector;
        $this->noCheckersLoaderReporter = $noCheckersLoaderReporter;
    }
    public function areSomeCheckerRegistered() : bool
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
    private function getCheckerCount() : int
    {
        $checkerCount = 0;
        $fileProcessors = $this->fileProcessorCollector->getFileProcessors();
        foreach ($fileProcessors as $fileProcessor) {
            $checkerCount += \count($fileProcessor->getCheckers());
        }
        return $checkerCount;
    }
}
