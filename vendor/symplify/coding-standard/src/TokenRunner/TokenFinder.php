<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
final class TokenFinder
{
    /**
     * @param Tokens<Token> $tokens
     * @param int|\PhpCsFixer\Tokenizer\Token $position
     */
    public function getPreviousMeaningfulToken(\PhpCsFixer\Tokenizer\Tokens $tokens, $position) : \PhpCsFixer\Tokenizer\Token
    {
        if (\is_int($position)) {
            return $this->findPreviousTokenByPosition($tokens, $position);
        }
        return $this->findPreviousTokenByToken($tokens, $position);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function findPreviousTokenByPosition(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : \PhpCsFixer\Tokenizer\Token
    {
        $previousPosition = $position - 1;
        if (!isset($tokens[$previousPosition])) {
            throw new \ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        return $tokens[$previousPosition];
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function findPreviousTokenByToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $positionToken) : \PhpCsFixer\Tokenizer\Token
    {
        $position = $this->resolvePositionByToken($tokens, $positionToken);
        return $this->findPreviousTokenByPosition($tokens, $position - 1);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function resolvePositionByToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $positionToken) : int
    {
        foreach ($tokens as $position => $token) {
            if ($token === $positionToken) {
                return $position;
            }
        }
        throw new \ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
}
