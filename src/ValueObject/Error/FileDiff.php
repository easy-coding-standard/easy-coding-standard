<?php

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo;
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
     * @param string $consoleFormattedDiff
     */
    public function __construct(\ECSPrefix20210516\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $diff, $consoleFormattedDiff, array $appliedCheckers)
    {
        $diff = (string) $diff;
        $consoleFormattedDiff = (string) $consoleFormattedDiff;
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
