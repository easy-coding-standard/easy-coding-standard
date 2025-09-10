<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersSupportedTokenizersTest
 */
namespace ECSPrefix202509\Fixtures\TestStandard\Sniffs\SupportedTokenizers;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
class ListensForCSSSniff implements Sniff
{
    public $supportedTokenizers = ['CSS'];
    public function register()
    {
        return [\T_WHITESPACE];
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
