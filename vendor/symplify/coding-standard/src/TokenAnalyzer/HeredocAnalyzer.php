<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class HeredocAnalyzer
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function isHerenowDoc(Tokens $tokens, BlockInfo $blockInfo) : bool
    {
        // heredoc/nowdoc => skip
        $nextToken = $this->getNextMeaningfulToken($tokens, $blockInfo->getStart());
        if (!$nextToken instanceof Token) {
            return \false;
        }
        return \strpos($nextToken->getContent(), '<<<') !== \false;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextMeaningfulToken(Tokens $tokens, int $index) : ?Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        return $tokens[$nextMeaningfulTokenPosition];
    }
}
