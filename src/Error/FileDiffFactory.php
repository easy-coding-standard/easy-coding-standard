<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Error;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix202208\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix202208\Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiffFactory
{
    /**
     * @var \Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param array<class-string<FixerInterface|Sniff>|string> $appliedCheckers
     */
    public function createFromDiffAndAppliedCheckers(SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers) : FileDiff
    {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        return new FileDiff($smartFileInfo->getRelativeFilePathFromCwd(), $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
