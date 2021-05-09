<?php

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;

final class ArrayAnalyzer
{
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;

    public function __construct(TokenSkipper $tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }

    /**
     * @param Tokens<Token> $tokens
     * @return int
     */
    public function getItemCount(Tokens $tokens, BlockInfo $blockInfo)
    {
        $nextMeanninfulPosition = $tokens->getNextMeaningfulToken($blockInfo->getStart());
        if ($nextMeanninfulPosition === null) {
            return 0;
        }

        /** @var Token $nextMeaningfulToken */
        $nextMeaningfulToken = $tokens[$nextMeanninfulPosition];

        // no elements
        if ($this->isArrayCloser($nextMeaningfulToken)) {
            return 0;
        }

        $itemCount = 1;
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, function (Token $token) use (&$itemCount) {
            if ($token->getContent() === ',') {
                ++$itemCount;
            }
        });

        return $itemCount;
    }

    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isIndexedList(Tokens $tokens, BlockInfo $blockInfo)
    {
        $isIndexedList = false;
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, function (Token $token) use (&$isIndexedList) {
            if ($token->isGivenKind(T_DOUBLE_ARROW)) {
                $isIndexedList = true;
            }
        });

        return $isIndexedList;
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function traverseArrayWithoutNesting(Tokens $tokens, BlockInfo $blockInfo, callable $callable)
    {
        for ($i = $blockInfo->getEnd() - 1; $i >= $blockInfo->getStart() + 1; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);

            /** @var Token $token */
            $token = $tokens[$i];
            $callable($token, $i, $tokens);
        }
    }

    /**
     * @return bool
     */
    private function isArrayCloser(Token $token)
    {
        if ($token->isGivenKind(CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return true;
        }

        return $token->getContent() === ')';
    }
}
