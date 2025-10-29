<?php

/**
 * Test fixture.
 *
 * @see \PHP_CodeSniffer\Tests\Core\RulesetPopulateTokenListenersNamingConventionsTest
 */
namespace ECSPrefix202510\BrokenNamingConventions\Sniffs;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
final class MissingCategoryDirSniff implements Sniff
{
    public function register()
    {
        return [\T_OPEN_TAG];
    }
    public function process(File $phpcsFile, $stackPtr)
    {
        // Do something.
    }
}
