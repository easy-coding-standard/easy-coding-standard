<?php

/**
 * Checks for usage of $this in static methods, which will cause runtime errors.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2023 Squiz Pty Ltd (ABN 77 084 670 600)
 * @copyright 2023 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Scope;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\AbstractScopeSniff;
use PHP_CodeSniffer\Util\Tokens;
class StaticThisUsageSniff extends AbstractScopeSniff
{
    /**
     * Constructs the test with the tokens it wishes to listen for.
     */
    public function __construct()
    {
        parent::__construct([\T_CLASS, \T_TRAIT, \T_ENUM, \T_ANON_CLASS], [\T_FUNCTION, \T_CLOSURE], \true);
    }
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     * @param int                         $currScope A pointer to the start of the scope.
     *
     * @return void
     */
    public function processTokenWithinScope(File $phpcsFile, int $stackPtr, int $currScope)
    {
        $tokens = $phpcsFile->getTokens();
        // Determine if this is a function which needs to be examined.
        if ($tokens[$stackPtr]['code'] === \T_FUNCTION) {
            $conditions = $tokens[$stackPtr]['conditions'];
            end($conditions);
            $deepestScope = key($conditions);
            if ($deepestScope !== $currScope) {
                return;
            }
            // Ignore abstract functions.
            if (isset($tokens[$stackPtr]['scope_closer']) === \false) {
                return;
            }
            $next = $phpcsFile->findNext(Tokens::EMPTY_TOKENS, $stackPtr + 1, null, \true);
            if ($next === \false || $tokens[$next]['code'] !== \T_STRING) {
                // Not a function declaration, or incomplete.
                return;
            }
            $type = 'method';
        } else {
            $type = 'closure';
        }
        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProps['is_static'] === \false) {
            return;
        }
        $next = $stackPtr;
        $end = $tokens[$stackPtr]['scope_closer'];
        $this->checkThisUsage($phpcsFile, $next, $end, $type);
    }
    /**
     * Check for $this variable usage between $next and $end tokens.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The current file being scanned.
     * @param int                         $next      The position of the next token to check.
     * @param int                         $end       The position of the last token to check.
     * @param string                      $type      Type of context being checked. Either 'method' or 'closure'.
     *
     * @return void
     */
    private function checkThisUsage(File $phpcsFile, int $next, int $end, string $type)
    {
        $tokens = $phpcsFile->getTokens();
        do {
            $next = $phpcsFile->findNext([\T_VARIABLE, \T_CLOSURE, \T_ANON_CLASS], $next + 1, $end);
            if ($next === \false) {
                continue;
            }
            if (($tokens[$next]['code'] === \T_ANON_CLASS || $tokens[$next]['code'] === \T_CLOSURE) && isset($tokens[$next]['scope_opener']) === \true) {
                $this->checkThisUsage($phpcsFile, $next, $tokens[$next]['scope_opener'], $type);
                $next = $tokens[$next]['scope_closer'];
                continue;
            }
            if ($tokens[$next]['content'] !== '$this') {
                continue;
            }
            $error = 'Usage of "$this" in a static %s will cause runtime errors';
            $data = [$type];
            $phpcsFile->addError($error, $next, 'Found', $data);
        } while ($next !== \false);
    }
    /**
     * Processes a token that is found within the scope that this test is
     * listening to.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The position in the stack where this
     *                                               token was found.
     *
     * @return void
     */
    protected function processTokenOutsideScope(File $phpcsFile, int $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if ($tokens[$stackPtr]['code'] !== \T_CLOSURE) {
            // We're only interested in closures when looking outside of OO.
            return;
        }
        $methodProps = $phpcsFile->getMethodProperties($stackPtr);
        if ($methodProps['is_static'] === \false) {
            return;
        }
        $this->checkThisUsage($phpcsFile, $stackPtr, $tokens[$stackPtr]['scope_closer'], 'closure');
    }
}
