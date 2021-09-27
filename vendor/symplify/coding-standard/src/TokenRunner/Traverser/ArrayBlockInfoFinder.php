<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Traverser;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
final class ArrayBlockInfoFinder
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder
     */
    private $blockFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return BlockInfo[]
     */
    public function findArrayOpenerBlockInfos(\PhpCsFixer\Tokenizer\Tokens $tokens) : array
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
     * @return Token[]|null[]
     */
    private function reverseTokens(\PhpCsFixer\Tokenizer\Tokens $tokens) : array
    {
        return \array_reverse($tokens->toArray(), \true);
    }
}
