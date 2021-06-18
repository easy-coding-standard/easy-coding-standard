<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject\Error;

use ECSPrefix20210618\Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiff
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $smartFileInfo;
    /**
     * @var string
     */
    private $diff;
    /**
     * @var string
     */
    private $consoleFormattedDiff;
    /**
     * @var mixed[]
     */
    private $appliedCheckers;
    /**
     * @param string[] $appliedCheckers
     */
    public function __construct(\ECSPrefix20210618\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $diff, string $consoleFormattedDiff, array $appliedCheckers)
    {
        $this->smartFileInfo = $smartFileInfo;
        $this->diff = $diff;
        $this->consoleFormattedDiff = $consoleFormattedDiff;
        $this->appliedCheckers = $appliedCheckers;
    }
    public function getDiff() : string
    {
        return $this->diff;
    }
    public function getDiffConsoleFormatted() : string
    {
        return $this->consoleFormattedDiff;
    }
    /**
     * @return string[]
     */
    public function getAppliedCheckers() : array
    {
        $this->appliedCheckers = \array_unique($this->appliedCheckers);
        \sort($this->appliedCheckers);
        return $this->appliedCheckers;
    }
    public function getRelativeFilePathFromCwd() : string
    {
        return $this->smartFileInfo->getRelativeFilePathFromCwd();
    }
}
