<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\Ruleset\ExpandRulesetReferenceInternalTest
 */
namespace ECSPrefix202505\Fixtures\Internal\Sniffs\Valid;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
class ValidSniff implements Sniff
{
    public function register()
    {
        return [\T_CLASS];
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
