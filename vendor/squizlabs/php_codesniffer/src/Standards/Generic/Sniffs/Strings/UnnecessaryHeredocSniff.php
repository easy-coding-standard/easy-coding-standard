<?php

/**
 * Prefer the use of nowdoc over heredoc.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2024 PHPCSStandards and contributors
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Strings;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
class UnnecessaryHeredocSniff implements Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array<int|string>
     */
    public function register()
    {
        return [\T_START_HEREDOC];
    }
    //end register()
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]['scope_closer']) === \false) {
            // Just to be safe. Shouldn't be possible as in that case, the opener shouldn't be tokenized
            // to T_START_HEREDOC by PHP.
            return;
            // @codeCoverageIgnore
        }
        $closer = $tokens[$stackPtr]['scope_closer'];
        $body = '';
        // Collect all the tokens within the heredoc body.
        for ($i = $stackPtr + 1; $i < $closer; $i++) {
            $body .= $tokens[$i]['content'];
        }
        $tokenizedBody = \token_get_all(\sprintf("<?php <<<EOD\n%s\nEOD;\n?>", $body));
        foreach ($tokenizedBody as $ptr => $bodyToken) {
            if (\is_array($bodyToken) === \false) {
                continue;
            }
            if ($bodyToken[0] === \T_DOLLAR_OPEN_CURLY_BRACES || $bodyToken[0] === \T_VARIABLE) {
                // Contains interpolation or expression.
                $phpcsFile->recordMetric($stackPtr, 'Heredoc contains interpolation or expression', 'yes');
                return;
            }
            if ($bodyToken[0] === \T_CURLY_OPEN && \is_array($tokenizedBody[$ptr + 1]) === \false && $tokenizedBody[$ptr + 1] === '$') {
                // Contains interpolation or expression.
                $phpcsFile->recordMetric($stackPtr, 'Heredoc contains interpolation or expression', 'yes');
                return;
            }
        }
        //end foreach
        $phpcsFile->recordMetric($stackPtr, 'Heredoc contains interpolation or expression', 'no');
        $warning = 'Detected heredoc without interpolation or expressions. Use nowdoc syntax instead';
        $fix = $phpcsFile->addFixableWarning($warning, $stackPtr, 'Found');
        if ($fix === \true) {
            $identifier = \trim(\ltrim($tokens[$stackPtr]['content'], '<'));
            $replaceWith = "'" . \trim($identifier, '"') . "'";
            $replacement = \str_replace($identifier, $replaceWith, $tokens[$stackPtr]['content']);
            $phpcsFile->fixer->replaceToken($stackPtr, $replacement);
        }
    }
    //end process()
}
//end class
