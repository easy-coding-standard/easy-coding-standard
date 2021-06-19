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
     * @var mixed[]
     */
    private $sources = [];
    /**
     * @var string
     */
    private $outputFormat = \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME;
    /**
     * @var bool
     */
    private $doesMatchGitDiff = \false;
    /**
     * @param string[] $sources
     */
    public function __construct(bool $isFixer = \false, bool $shouldClearCache = \false, bool $showProgressBar = \true, bool $showErrorTable = \true, array $sources = [], string $outputFormat = \Symplify\EasyCodingStandard\Console\Output\ConsoleOutputFormatter::NAME, bool $doesMatchGitDiff = \false)
    {
        $this->isFixer = $isFixer;
        $this->shouldClearCache = $shouldClearCache;
        $this->showProgressBar = $showProgressBar;
        $this->showErrorTable = $showErrorTable;
        $this->sources = $sources;
        $this->outputFormat = $outputFormat;
        $this->doesMatchGitDiff = $doesMatchGitDiff;
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
    public function doesMatchGitDiff() : bool
    {
        return $this->doesMatchGitDiff;
    }
}
