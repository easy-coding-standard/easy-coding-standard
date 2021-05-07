<?php

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiff
{
    /**
     * @var string
     */
    private $diff;
    /**
     * @var string[]
     */
    private $appliedCheckers = [];
    /**
     * @var string
     */
    private $consoleFormattedDiff;
    /**
     * @var SmartFileInfo
     */
    private $smartFileInfo;
    /**
     * @param string[] $appliedCheckers
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param string $diff
     * @param string $consoleFormattedDiff
     */
    public function __construct($smartFileInfo, $diff, $consoleFormattedDiff, array $appliedCheckers)
    {
        $this->diff = $diff;
        $this->appliedCheckers = $appliedCheckers;
        $this->consoleFormattedDiff = $consoleFormattedDiff;
        $this->smartFileInfo = $smartFileInfo;
    }
    /**
     * @return string
     */
    public function getDiff()
    {
        return $this->diff;
    }
    /**
     * @return string
     */
    public function getDiffConsoleFormatted()
    {
        return $this->consoleFormattedDiff;
    }
    /**
     * @return mixed[]
     */
    public function getAppliedCheckers()
    {
        $this->appliedCheckers = \array_unique($this->appliedCheckers);
        \sort($this->appliedCheckers);
        return $this->appliedCheckers;
    }
    /**
     * @return string
     */
    public function getRelativeFilePathFromCwd()
    {
        return $this->smartFileInfo->getRelativeFilePathFromCwd();
    }
}
