<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\DI\Source;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class AnotherSniff implements Sniff
{
    public $lineLimit;

    public $absoluteLineLimit;

    public function register()
    {
        return [T_WHILE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
    }
}
