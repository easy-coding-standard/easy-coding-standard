<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class LineLengthTransformer
{
    /**
     * @var LineLengthResolver
     */
    private $lineLengthResolver;
    /**
     * @var TokensInliner
     */
    private $tokensInliner;
    /**
     * @var TokensNewliner
     */
    private $tokensNewliner;
    /**
     * @var FirstLineLengthResolver
     */
    private $firstLineLengthResolver;
    /**
     * @param \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthResolver $lineLengthResolver
     * @param \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensInliner $tokensInliner
     * @param \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\FirstLineLengthResolver $firstLineLengthResolver
     * @param \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner $tokensNewliner
     */
    public function __construct($lineLengthResolver, $tokensInliner, $firstLineLengthResolver, $tokensNewliner)
    {
        $this->lineLengthResolver = $lineLengthResolver;
        $this->tokensInliner = $tokensInliner;
        $this->firstLineLengthResolver = $firstLineLengthResolver;
        $this->tokensNewliner = $tokensNewliner;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @param int $lineLength
     * @param bool $breakLongLines
     * @param bool $inlineShortLine
     */
    public function fixStartPositionToEndPosition($blockInfo, $tokens, $lineLength, $breakLongLines, $inlineShortLine)
    {
        if ($this->hasPromotedProperty($tokens, $blockInfo)) {
            return;
        }
        $firstLineLength = $this->firstLineLengthResolver->resolveFromTokensAndStartPosition($tokens, $blockInfo);
        if ($firstLineLength > $lineLength && $breakLongLines) {
            $this->tokensNewliner->breakItems($blockInfo, $tokens);
            return;
        }
        $fullLineLength = $this->lineLengthResolver->getLengthFromStartEnd($tokens, $blockInfo);
        if ($fullLineLength <= $lineLength && $inlineShortLine) {
            $this->tokensInliner->inlineItems($tokens, $blockInfo);
            return;
        }
    }
    /**
     * @param Tokens<Token> $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return bool
     */
    private function hasPromotedProperty($tokens, $blockInfo)
    {
        $resultByKind = $tokens->findGivenKind([\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE], $blockInfo->getStart(), $blockInfo->getEnd());
        return (bool) \array_filter($resultByKind);
    }
}
