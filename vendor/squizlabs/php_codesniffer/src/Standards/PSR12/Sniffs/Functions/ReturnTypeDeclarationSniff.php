<?php

/**
 * Ensure return types are defined correctly for functions and closures.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2023 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2023 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
class ReturnTypeDeclarationSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [\T_FUNCTION, \T_CLOSURE, \T_FN];
    }
    /**
     * Processes this test when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['parenthesis_opener']) === \false || isset($tokens[$stackPtr]['parenthesis_closer']) === \false) {
            return;
        }
        $methodProperties = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProperties['return_type'] === '') {
            return;
        }
        $returnType = $methodProperties['return_type_token'];
        if ($methodProperties['nullable_return_type'] === \true) {
            $returnType = $phpcsFile->findPrevious(\T_NULLABLE, $returnType - 1);
        }
        $colon = $phpcsFile->findPrevious(\T_COLON, $returnType - 1, $tokens[$stackPtr]['parenthesis_closer']);
        if ($colon === \false) {
            // Parse error / live coding.
            return;
        }
        if ($tokens[$returnType - 1]['code'] !== \T_WHITESPACE || $tokens[$returnType - 1]['content'] !== ' ' || $returnType - 2 !== $colon) {
            $error = 'There must be a single space between the colon and type in a return type declaration';
            $nonWhitespaceToken = $phpcsFile->findNext(\T_WHITESPACE, $colon + 1, $returnType, \true);
            if ($nonWhitespaceToken !== \false) {
                $phpcsFile->addError($error, $returnType, 'SpaceBeforeReturnType');
            } else {
                $fix = $phpcsFile->addFixableError($error, $returnType, 'SpaceBeforeReturnType');
                if ($fix === \true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = $returnType - 1; $i > $colon; $i--) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }
                    $phpcsFile->fixer->addContentBefore($returnType, ' ');
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }
        if ($tokens[$colon - 1]['code'] !== \T_CLOSE_PARENTHESIS) {
            $error = 'There must not be a space before the colon in a return type declaration';
            $prev = $phpcsFile->findPrevious(\T_WHITESPACE, $colon - 1, null, \true);
            if ($tokens[$prev]['code'] === \T_CLOSE_PARENTHESIS) {
                $fix = $phpcsFile->addFixableError($error, $colon, 'SpaceBeforeColon');
                if ($fix === \true) {
                    $phpcsFile->fixer->beginChangeset();
                    for ($x = $prev + 1; $x < $colon; $x++) {
                        $phpcsFile->fixer->replaceToken($x, '');
                    }
                    $phpcsFile->fixer->endChangeset();
                }
            } else {
                $phpcsFile->addError($error, $colon, 'SpaceBeforeColon');
            }
        }
    }
}
