<?php

namespace Symplify\EasyCodingStandard\Error;

use ECSPrefix20210514\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiffFactory
{
    /**
     * @var ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(\ECSPrefix20210514\Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param string[] $appliedCheckers
     * @param string $diff
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff
     */
    public function createFromDiffAndAppliedCheckers(\ECSPrefix20210514\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, $diff, array $appliedCheckers)
    {
        $diff = (string) $diff;
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        return new \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff($smartFileInfo, $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
