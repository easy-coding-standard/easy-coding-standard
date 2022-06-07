<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject;

use Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter;
final class Configuration
{
    /**
     * @var bool
     */
    private $isFixer = \false;
    /**
     * @var bool
     */
    private $shouldClearCache = \false;
    /**
     * @var bool
     */
    private $showProgressBar = \true;
    /**
     * @var bool
     */
    private $showErrorTable = \true;
    /**
     * @var string[]
     */
    private $sources = [];
    /**
     * @var string
     */
    private $outputFormat = ConsoleOutputFormatter::NAME;
    /**
     * @var bool
     */
    private $isParallel = \false;
    /**
     * @var string|null
     */
    private $config;
    /**
     * @var string|null
     */
    private $parallelPort = null;
    /**
     * @var string|null
     */
    private $parallelIdentifier = null;
    /**
     * @var string|null
     */
    private $memoryLimit = null;
    /**
     * @param string[] $sources
     * @param string|null $parallelPort
     * @param string|null $parallelIdentifier
     * @param string|null $memoryLimit
     */
    public function __construct(bool $isFixer = \false, bool $shouldClearCache = \false, bool $showProgressBar = \true, bool $showErrorTable = \true, array $sources = [], string $outputFormat = ConsoleOutputFormatter::NAME, bool $isParallel = \false, ?string $config = null, $parallelPort = null, $parallelIdentifier = null, $memoryLimit = null)
    {
        $this->isFixer = $isFixer;
        $this->shouldClearCache = $shouldClearCache;
        $this->showProgressBar = $showProgressBar;
        $this->showErrorTable = $showErrorTable;
        $this->sources = $sources;
        $this->outputFormat = $outputFormat;
        $this->isParallel = $isParallel;
        $this->config = $config;
        $this->parallelPort = $parallelPort;
        $this->parallelIdentifier = $parallelIdentifier;
        $this->memoryLimit = $memoryLimit;
    }
    public function isFixer() : bool
    {
        return $this->isFixer;
    }
    public function shouldClearCache() : bool
    {
        return $this->shouldClearCache;
    }
    public function shouldShowProgressBar() : bool
    {
        return $this->showProgressBar;
    }
    public function shouldShowErrorTable() : bool
    {
        return $this->showErrorTable;
    }
    /**
     * @return string[]
     */
    public function getSources() : array
    {
        return $this->sources;
    }
    public function getOutputFormat() : string
    {
        return $this->outputFormat;
    }
    public function isParallel() : bool
    {
        return $this->isParallel;
    }
    public function getConfig() : ?string
    {
        return $this->config;
    }
    public function getParallelPort() : ?string
    {
        return $this->parallelPort;
    }
    public function getParallelIdentifier() : ?string
    {
        return $this->parallelIdentifier;
    }
    public function getMemoryLimit() : ?string
    {
        return $this->memoryLimit;
    }
}
