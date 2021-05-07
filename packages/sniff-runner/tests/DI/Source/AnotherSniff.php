<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\DI\Source;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

final class AnotherSniff implements Sniff
{
    public function register()
    {
        return [T_WHILE];
    }

    public function process(File $phpcsFile, $stackPtr)
    {
    }
}
