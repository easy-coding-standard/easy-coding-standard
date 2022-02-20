<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Error;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
final class FileDiffFactory
{
    /**
     * @var \Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter
     */
    private $colorConsoleDiffFormatter;
    public function __construct(\ECSPrefix20220220\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter $colorConsoleDiffFormatter)
    {
        $this->colorConsoleDiffFormatter = $colorConsoleDiffFormatter;
    }
    /**
     * @param array<class-string<FixerInterface|Sniff>|string> $appliedCheckers
     */
    public function createFromDiffAndAppliedCheckers(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $smartFileInfo, string $diff, array $appliedCheckers) : \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff
    {
        $consoleFormattedDiff = $this->colorConsoleDiffFormatter->format($diff);
        return new \Symplify\EasyCodingStandard\ValueObject\Error\FileDiff($smartFileInfo->getRelativeFilePathFromCwd(), $diff, $consoleFormattedDiff, $appliedCheckers);
    }
}
