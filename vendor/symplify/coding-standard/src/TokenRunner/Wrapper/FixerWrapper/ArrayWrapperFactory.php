<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\Wrapper\FixerWrapper\ArrayWrapper;
final class ArrayWrapperFactory
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\TokenSkipper
     */
    private $tokenSkipper;
    public function __construct(TokenSkipper $tokenSkipper)
    {
        $this->tokenSkipper = $tokenSkipper;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function createFromTokensAndBlockInfo(Tokens $tokens, BlockInfo $blockInfo) : ArrayWrapper
    {
        return new ArrayWrapper($tokens, $blockInfo, $this->tokenSkipper);
    }
}
