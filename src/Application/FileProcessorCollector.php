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
     */
    public function __construct(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor $sniffFileProcessor, \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor $fixerFileProcessor)
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
