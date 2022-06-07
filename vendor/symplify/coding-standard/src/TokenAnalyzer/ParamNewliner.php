<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class ParamNewliner
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder
     */
    private $blockFinder;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner
     */
    private $tokensNewliner;
    public function __construct(BlockFinder $blockFinder, TokensNewliner $tokensNewliner)
    {
        $this->blockFinder = $blockFinder;
        $this->tokensNewliner = $tokensNewliner;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function processFunction(Tokens $tokens, int $position) : void
    {
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (!$blockInfo instanceof BlockInfo) {
            return;
        }
        $this->tokensNewliner->breakMultipleItems($blockInfo, $tokens, LineKind::CALLS);
    }
}
