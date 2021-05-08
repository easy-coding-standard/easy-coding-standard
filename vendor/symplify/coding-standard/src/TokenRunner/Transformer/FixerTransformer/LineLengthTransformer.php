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
    public function __construct(\Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthResolver $lineLengthResolver, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensInliner $tokensInliner, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\FirstLineLengthResolver $firstLineLengthResolver, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner $tokensNewliner)
    {
        $this->lineLengthResolver = $lineLengthResolver;
        $this->tokensInliner = $tokensInliner;
        $this->firstLineLengthResolver = $firstLineLengthResolver;
        $this->tokensNewliner = $tokensNewliner;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $lineLength
     * @param bool $breakLongLines
     * @param bool $inlineShortLine
     */
    public function fixStartPositionToEndPosition(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens, $lineLength, $breakLongLines, $inlineShortLine)
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
     * @return bool
     */
    private function hasPromotedProperty(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo)
    {
        $resultByKind = $tokens->findGivenKind([\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE], $blockInfo->getStart(), $blockInfo->getEnd());
        return (bool) \array_filter($resultByKind);
    }
}
