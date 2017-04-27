<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Contract;

use PHP_CodeSniffer\Sniffs\Sniff;
use PhpCsFixer\Fixer\FixerInterface;

interface SkipperInterface
{
    /**
     * @param Sniff|FixerInterface|string $checker
     */
    public function shouldSkipCheckerAndFile($checker, string $relativeFilePath): bool;
}
