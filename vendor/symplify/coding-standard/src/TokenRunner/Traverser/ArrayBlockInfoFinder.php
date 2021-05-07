<?php

namespace Symplify\CodingStandard\TokenRunner\Traverser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
final class ArrayBlockInfoFinder
{
    /**
     * @var BlockFinder
     */
    private $blockFinder;
    /**
     * @param \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder
     */
    public function __construct($blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return mixed[]
     */
    public function findArrayOpenerBlockInfos($tokens)
    {
        $reversedTokens = $this->reverseTokens($tokens);
        $blockInfos = [];
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind(\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds::ARRAY_OPEN_TOKENS)) {
                continue;
            }
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
                continue;
            }
            $blockInfos[] = $blockInfo;
        }
        return $blockInfos;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return mixed[]
     */
    private function reverseTokens($tokens)
    {
        return \array_reverse($tokens->toArray(), \true);
    }
}
