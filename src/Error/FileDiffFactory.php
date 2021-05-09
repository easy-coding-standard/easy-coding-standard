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

    public function __construct(ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }

    /**
     * @param string[] $appliedCheckers
     * @param string $diff
     * @return \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff
     */
    public function createFromDiffAndAppliedCheckers(
        SmartFileInfo $smartFileInfo,
        $diff,
        array $appliedCheckers
    ) {
        $diff = (string) $diff;
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        return new FileDiff($smartFileInfo, $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
