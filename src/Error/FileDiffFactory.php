<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use SplFileInfo;
use Symplify\EasyCodingStandard\FileSystem\StaticRelativeFilePathHelper;
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
        string $filePath,
        string $diff,
        array  $appliedCheckers
    ): FileDiff {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);

        $relativeFilePath = StaticRelativeFilePathHelper::resolveFromCwd($filePath);

        return new FileDiff($relativeFilePath, $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
