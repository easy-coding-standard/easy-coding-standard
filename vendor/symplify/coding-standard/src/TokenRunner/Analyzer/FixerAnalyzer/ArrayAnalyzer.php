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
    /**
     * @param \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper $tokenSkipper
     */
    public function __construct($tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }
    /**
     * @param Tokens<Token> $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return int
     */
    public function getItemCount($tokens, $blockInfo)
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
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, function (\PhpCsFixer\Tokenizer\Token $token) use(&$itemCount) {
            if ($token->getContent() === ',') {
                ++$itemCount;
            }
        });
        return $itemCount;
    }
    /**
     * @param Tokens<Token> $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return bool
     */
    public function isIndexedList($tokens, $blockInfo)
    {
        $isIndexedList = \false;
        $this->traverseArrayWithoutNesting($tokens, $blockInfo, function (\PhpCsFixer\Tokenizer\Token $token) use(&$isIndexedList) {
            if ($token->isGivenKind(\T_DOUBLE_ARROW)) {
                $isIndexedList = \true;
            }
        });
        return $isIndexedList;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     */
    public function traverseArrayWithoutNesting($tokens, $blockInfo, callable $callable)
    {
        for ($i = $blockInfo->getEnd() - 1; $i >= $blockInfo->getStart() + 1; --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($tokens, $i);
            /** @var Token $token */
            $token = $tokens[$i];
            $callable($token, $i, $tokens);
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Token $token
     * @return bool
     */
    private function isArrayCloser($token)
    {
        if ($token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return \true;
        }
        return $token->getContent() === ')';
    }
}
