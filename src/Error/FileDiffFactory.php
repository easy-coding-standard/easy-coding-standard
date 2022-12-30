<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileDiffFactory
{
    public function __construct(
        private ColorConsoleDiffFormatter $colorConsoleDiffFormatter
    ) {
    }

    /**
     * @param array<class-string<FixerInterface|Sniff>|string> $appliedCheckers
     */
    public function createFromDiffAndAppliedCheckers(
        SmartFileInfo $smartFileInfo,
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
