<?php

namespace Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class ArrayWrapper
{
    /**
     * @var int[]
     */
    const ARRAY_OPEN_TOKENS = [\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN];
    /**
     * @var Tokens
     */
    private $tokens;
    /**
     * @var TokenSkipper
     */
    private $tokenSkipper;
    /**
     * @var BlockInfo
     */
    private $blockInfo;
    public function __construct(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper $tokenSkipper)
    {
        $this->tokens = $tokens;
        $this->tokenSkipper = $tokenSkipper;
        $this->blockInfo = $blockInfo;
    }
    /**
     * @return bool
     */
    public function isAssociativeArray()
    {
        for ($i = $this->blockInfo->getStart() + 1; $i <= $this->blockInfo->getEnd() - 1; ++$i) {
            $i = $this->tokenSkipper->skipBlocks($this->tokens, $i);
            $token = $this->tokens[$i];
            if ($token->isGivenKind(\T_DOUBLE_ARROW)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return int
     */
    public function getItemCount()
    {
        $itemCount = 0;
        for ($i = $this->blockInfo->getEnd() - 1; $i >= $this->blockInfo->getStart(); --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);
            $token = $this->tokens[$i];
            if ($token->isGivenKind(\T_DOUBLE_ARROW)) {
                ++$itemCount;
            }
        }
        return $itemCount;
    }
    /**
     * @return bool
     */
    public function isFirstItemArray()
    {
        for ($i = $this->blockInfo->getEnd() - 1; $i >= $this->blockInfo->getStart(); --$i) {
            $i = $this->tokenSkipper->skipBlocksReversed($this->tokens, $i);
            /** @var Token $token */
            $token = $this->tokens[$i];
            if ($token->isGivenKind(\T_DOUBLE_ARROW)) {
                $nextTokenAfterArrowPosition = $this->tokens->getNextNonWhitespace($i);
                if ($nextTokenAfterArrowPosition === null) {
                    return \false;
                }
                /** @var Token $nextToken */
                $nextToken = $this->tokens[$nextTokenAfterArrowPosition];
                return $nextToken->isGivenKind(self::ARRAY_OPEN_TOKENS);
            }
        }
        return \false;
    }
}
