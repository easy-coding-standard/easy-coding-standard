<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Traverser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
final class ArrayBlockInfoFinder
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder
     */
    private $blockFinder;
    public function __construct(BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return BlockInfo[]
     */
    public function findArrayOpenerBlockInfos(Tokens $tokens) : array
    {
        $reversedTokens = $this->reverseTokens($tokens);
        $blockInfos = [];
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind(TokenKinds::ARRAY_OPEN_TOKENS)) {
                continue;
            }
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if (!$blockInfo instanceof BlockInfo) {
                continue;
            }
            $blockInfos[] = $blockInfo;
        }
        return $blockInfos;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return Token[]|null[]
     */
    private function reverseTokens(Tokens $tokens) : array
    {
        return \array_reverse($tokens->toArray(), \true);
    }
}
