<?php

namespace Symplify\EasyCodingStandard\Application;

use Symplify\EasyCodingStandard\Contract\Application\FileProcessorInterface;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
final class FileProcessorCollector
{
    /**
     * @var FileProcessorInterface[]
     */
    private $fileProcessors = [];
    /**
     * orders matters, so Fixer can cleanup after Sniffer
     * @param \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor
     * @param \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor
     */
    public function __construct($sniffFileProcessor, $fixerFileProcessor)
    {
        $this->fileProcessors[] = $sniffFileProcessor;
        $this->fileProcessors[] = $fixerFileProcessor;
    }
    /**
     * @return mixed[]
     */
    public function getFileProcessors()
    {
        return $this->fileProcessors;
    }
}
