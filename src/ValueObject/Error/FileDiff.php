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
     * @param string $diff
     */
    public function __construct(\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $diff, string $consoleFormattedDiff, array $appliedCheckers)
    {
        if (\is_object($diff)) {
            $diff = (string) $diff;
        }
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
