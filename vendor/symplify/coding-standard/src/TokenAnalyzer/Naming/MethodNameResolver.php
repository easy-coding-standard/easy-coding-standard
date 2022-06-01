<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenAnalyzer\Naming;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220601\Symplify\PackageBuilder\ValueObject\MethodName;
final class MethodNameResolver
{
    /**
     * @param Tokens<Token> $tokens
     * @param MethodName::* $desiredMethodName
     */
    public function isMethodName(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position, string $desiredMethodName) : bool
    {
        $methodName = $this->getMethodName($tokens, $position);
        if (!\is_string($methodName)) {
            return \false;
        }
        return $methodName === $desiredMethodName;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function getMethodName(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : ?string
    {
        /** @var Token $currentToken */
        $currentToken = $tokens[$position];
        if (!$currentToken->isGivenKind(\T_FUNCTION)) {
            return null;
        }
        $nextToken = $this->getNextMeaningfulToken($tokens, $position);
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return null;
        }
        return $nextToken->getContent();
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextMeaningfulToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : ?\PhpCsFixer\Tokenizer\Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        return $tokens[$nextMeaningfulTokenPosition];
    }
}
