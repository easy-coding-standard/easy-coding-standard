<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\RegisterSniffsRejectsInvalidSniffTest
 */
namespace ECSPrefix202510\Fixtures\TestStandard\Sniffs\InvalidSniffError;

use PHP_CodeSniffer\Files\File;
final class NoImplementsNoRegisterSniff
{
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
