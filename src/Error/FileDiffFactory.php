<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\ConsoleColorDiff\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;

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
     */
    public function createFromDiffAndAppliedCheckers(string $diff, array $appliedCheckers): FileDiff
    {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        return new FileDiff($diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
