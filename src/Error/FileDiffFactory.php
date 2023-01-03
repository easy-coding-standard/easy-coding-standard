<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;

final class FileDiffFactory
{
    public function __construct(
        private readonly ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
    }

    /**
     * @param array<class-string<FixerInterface|Sniff>|string> $appliedCheckers
     */
    public function createFromDiffAndAppliedCheckers(
        \SplFileInfo $smartFileInfo,
        string $diff,
        array $appliedCheckers
    ): FileDiff {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        return new FileDiff(
            $smartFileInfo->getRelativeFilePathFromCwd(),
            $diff,
            $consoleFormattedDiff,
            $appliedCheckers
        );
    }
}
