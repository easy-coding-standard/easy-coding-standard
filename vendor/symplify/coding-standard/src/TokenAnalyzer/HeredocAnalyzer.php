<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class HeredocAnalyzer
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function isHerenowDoc(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo) : bool
    {
        // heredoc/nowdoc => skip
        $nextToken = $this->getNextMeaningfulToken($tokens, $blockInfo->getStart());
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return \false;
        }
        return \strpos($nextToken->getContent(), '<<<') !== \false;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextMeaningfulToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : ?\PhpCsFixer\Tokenizer\Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        return $tokens[$nextMeaningfulTokenPosition];
    }
}
