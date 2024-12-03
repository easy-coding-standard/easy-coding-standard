<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Generators\GeneratorTest
 */
namespace ECSPrefix202412\Fixtures\StandardWithDocs\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
abstract class DummySniff implements Sniff
{
    public function register()
    {
        return [\T_WHITESPACE];
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
