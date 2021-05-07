<?php

namespace Symplify\EasyCodingStandard\Error;

use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiffFactory
{
    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    /**
     * @param \Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter
     */
    public function __construct($colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param string[] $appliedCheckers
     * @param \Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo
     * @param string $diff
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff
     */
    public function createFromDiffAndAppliedCheckers($smartFileInfo, $diff, array $appliedCheckers)
    {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        return new FileDiff($smartFileInfo, $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
