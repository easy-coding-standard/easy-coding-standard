<?php

namespace Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20210514\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TokenSkipper
{
    /**
     * @var BlockFinder
     */
    private $blockFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder)
    {
        $this->blockFinder = $blockFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return int
     */
    public function skipBlocks(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $position = (int) $position;
        if (!isset($tokens[$position])) {
            throw new \Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException($position);
        }
        $token = $tokens[$position];
        if ($token->getContent() === '{') {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
                return $position;
            }
            return $blockInfo->getEnd();
        }
        if ($token->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, \T_ARRAY])) {
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
            if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
                return $position;
            }
            return $blockInfo->getEnd();
        }
        return $position;
    }
    /**
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return int
     */
    public function skipBlocksReversed(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $position = (int) $position;
        /** @var Token $token */
        $token = $tokens[$position];
        if (!$token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE) && !$token->equals(')')) {
            return $position;
        }
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
            throw new \ECSPrefix20210514\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $blockInfo->getStart();
    }
}
