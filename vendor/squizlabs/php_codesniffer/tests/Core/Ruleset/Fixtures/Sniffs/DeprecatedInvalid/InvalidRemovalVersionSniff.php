<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\SniffDeprecationTest
 */
namespace ECSPrefix202408\Fixtures\Sniffs\DeprecatedInvalid;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\DeprecatedSniff;
use PHP_CodeSniffer\Sniffs\Sniff;
class InvalidRemovalVersionSniff implements Sniff, DeprecatedSniff
{
    public function getDeprecationVersion()
    {
        return 'dummy';
    }
    public function getRemovalVersion()
    {
        return ['4.0'];
    }
    public function getDeprecationMessage()
    {
        return 'dummy';
    }
    public function register()
    {
        return [\T_WHITESPACE];
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
