<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\PopulateTokenListenersTest
 */
namespace ECSPrefix202505\Fixtures\TestStandard\Sniffs\InvalidSniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
class RegisterNoArraySniff implements Sniff
{
    public function register()
    {
        return \false;
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
