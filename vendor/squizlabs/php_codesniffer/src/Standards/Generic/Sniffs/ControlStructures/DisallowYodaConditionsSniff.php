<?php

/**
 * Ban the use of Yoda conditions.
 *
 * @author    Mponos George <gmponos@gmail.com>
 * @author    Mark Scherer <username@example.com>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\Generic\Sniffs\ControlStructures;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;
class DisallowYodaConditionsSniff implements \PHP_CodeSniffer\Sniffs\Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return \PHP_CodeSniffer\Util\Tokens::$comparisonTokens;
    }
    //end register()
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        $previousIndex = $phpcsFile->findPrevious(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $stackPtr - 1, null, \true);
        $relevantTokens = [T_CLOSE_SHORT_ARRAY, T_CLOSE_PARENTHESIS, T_TRUE, T_FALSE, T_NULL, \T_LNUMBER, \T_DNUMBER, \T_CONSTANT_ENCAPSED_STRING];
        if ($previousIndex === \false || \in_array($tokens[$previousIndex]['code'], $relevantTokens, \true) === \false) {
            return;
        }
        if ($tokens[$previousIndex]['code'] === T_CLOSE_SHORT_ARRAY) {
            $previousIndex = $tokens[$previousIndex]['bracket_opener'];
            if ($this->isArrayStatic($phpcsFile, $previousIndex) === \false) {
                return;
            }
        }
        $prevIndex = $phpcsFile->findPrevious(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $previousIndex - 1, null, \true);
        if ($prevIndex === \false) {
            return;
        }
        if (\in_array($tokens[$prevIndex]['code'], \PHP_CodeSniffer\Util\Tokens::$arithmeticTokens, \true) === \true) {
            return;
        }
        if ($tokens[$prevIndex]['code'] === T_STRING_CONCAT) {
            return;
        }
        // Is it a parenthesis.
        if ($tokens[$previousIndex]['code'] === T_CLOSE_PARENTHESIS) {
            // Check what exists inside the parenthesis.
            $closeParenthesisIndex = $phpcsFile->findPrevious(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $tokens[$previousIndex]['parenthesis_opener'] - 1, null, \true);
            if ($closeParenthesisIndex === \false || $tokens[$closeParenthesisIndex]['code'] !== \T_ARRAY) {
                if ($tokens[$closeParenthesisIndex]['code'] === \T_STRING) {
                    return;
                }
                // If it is not an array check what is inside.
                $found = $phpcsFile->findPrevious(\T_VARIABLE, $previousIndex - 1, $tokens[$previousIndex]['parenthesis_opener']);
                // If a variable exists, it is not Yoda.
                if ($found !== \false) {
                    return;
                }
                // If there is nothing inside the parenthesis, it it not a Yoda.
                $opener = $tokens[$previousIndex]['parenthesis_opener'];
                $prev = $phpcsFile->findPrevious(\PHP_CodeSniffer\Util\Tokens::$emptyTokens, $previousIndex - 1, $opener + 1, \true);
                if ($prev === \false) {
                    return;
                }
            } else {
                if ($tokens[$closeParenthesisIndex]['code'] === \T_ARRAY && $this->isArrayStatic($phpcsFile, $closeParenthesisIndex) === \false) {
                    return;
                }
            }
            //end if
        }
        //end if
        $phpcsFile->addError('Usage of Yoda conditions is not allowed; switch the expression order', $stackPtr, 'Found');
    }
    //end process()
    /**
     * Determines if an array is a static definition.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile  The file being scanned.
     * @param int                         $arrayToken The position of the array token.
     *
     * @return bool
     */
    public function isArrayStatic(\PHP_CodeSniffer\Files\File $phpcsFile, $arrayToken)
    {
        $tokens = $phpcsFile->getTokens();
        $arrayEnd = null;
        if ($tokens[$arrayToken]['code'] === T_OPEN_SHORT_ARRAY) {
            $start = $arrayToken;
            $end = $tokens[$arrayToken]['bracket_closer'];
        } else {
            if ($tokens[$arrayToken]['code'] === \T_ARRAY) {
                $start = $tokens[$arrayToken]['parenthesis_opener'];
                $end = $tokens[$arrayToken]['parenthesis_closer'];
            } else {
                return \true;
            }
        }
        $staticTokens = \PHP_CodeSniffer\Util\Tokens::$emptyTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$textStringTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$assignmentTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$equalityTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$comparisonTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$arithmeticTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$operators;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$booleanOperators;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$castTokens;
        $staticTokens += \PHP_CodeSniffer\Util\Tokens::$bracketTokens;
        $staticTokens += [\T_DOUBLE_ARROW => \T_DOUBLE_ARROW, T_COMMA => T_COMMA, T_TRUE => T_TRUE, T_FALSE => T_FALSE];
        for ($i = $start + 1; $i < $end; $i++) {
            if (isset($tokens[$i]['scope_closer']) === \true) {
                $i = $tokens[$i]['scope_closer'];
                continue;
            }
            if (isset($staticTokens[$tokens[$i]['code']]) === \false) {
                return \false;
            }
        }
        return \true;
    }
    //end isArrayStatic()
}
//end class
