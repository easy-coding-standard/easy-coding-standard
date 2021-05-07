<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class TokensInliner
{
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;
    /**
     * @param \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper $tokenSkipper
     */
    public function __construct($tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return void
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     */
    public function inlineItems($tokens, $blockInfo)
    {
        // replace line feeds with " "
        for ($i = $blockInfo->getStart() + 1; $i < $blockInfo->getEnd(); ++$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];
            $i = $this->tokenSkipper->skipBlocks($tokens, $i);
            if (!$currentToken->isGivenKind(\T_WHITESPACE)) {
                continue;
            }
            /** @var Token $previousToken */
            $previousToken = $tokens[$i - 1];
            /** @var Token $nextToken */
            $nextToken = $tokens[$i + 1];
            // do not clear before *doc end, removing spaces breaks stuff
            if ($previousToken->isGivenKind([\T_START_HEREDOC, \T_END_HEREDOC])) {
                continue;
            }
            // clear space after opening and before closing bracket
            if ($this->isBlockStartOrEnd($previousToken, $nextToken)) {
                $tokens->clearAt($i);
                continue;
            }
            $tokens[$i] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Token $previousToken
     * @param \PhpCsFixer\Tokenizer\Token $nextToken
     * @return bool
     */
    private function isBlockStartOrEnd($previousToken, $nextToken)
    {
        if (\in_array($previousToken->getContent(), ['(', '['], \true)) {
            return \true;
        }
        return \in_array($nextToken->getContent(), [')', ']'], \true);
    }
}
