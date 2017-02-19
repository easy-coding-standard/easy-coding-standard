<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Application;

use Symplify\EasyCodingStandard\FixerRunner\Runner\Runner;

final class FileProcessor
{
    /**
     * @var Runner
     */
    private $runner;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
    }

    public function processFiles(array $files, bool $isFixer)
    {
        $this->runner->fix($files, $isFixer);
    }
}
