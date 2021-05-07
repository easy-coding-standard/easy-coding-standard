<?php

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class CallAnalyzer
{
    /**
     * @param Tokens<Token> $tokens
     * @param int $bracketPosition
     * @return bool
     */
    public function isMethodCall($tokens, $bracketPosition)
    {
        $objectToken = new \PhpCsFixer\Tokenizer\Token([\T_OBJECT_OPERATOR, '->']);
        $whitespaceToken = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
        $previousTokenOfKindPosition = $tokens->getPrevTokenOfKind($bracketPosition, [$objectToken, $whitespaceToken]);
        // probably a function call
        if ($previousTokenOfKindPosition === null) {
            return \false;
        }
        /** @var Token $token */
        $token = $tokens[$previousTokenOfKindPosition];
        return $token->isGivenKind(\T_OBJECT_OPERATOR);
    }
}
