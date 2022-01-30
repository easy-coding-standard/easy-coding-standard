<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
abstract class AbstractSymplifyFixer implements \PhpCsFixer\Fixer\FixerInterface
{
    public function getPriority() : int
    {
        return 0;
    }
    public function getName() : string
    {
        return self::class;
    }
    public function isRisky() : bool
    {
        return \false;
    }
    public function supports(\SplFileInfo $file) : bool
    {
        return \true;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return Token[]
     */
    protected function reverseTokens(\PhpCsFixer\Tokenizer\Tokens $tokens) : array
    {
        $reversedTokens = \array_reverse($tokens->toArray(), \true);
        // remove null values
        return \array_filter($reversedTokens);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function getNextMeaningfulToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : ?\PhpCsFixer\Tokenizer\Token
    {
        $nextMeaninfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaninfulTokenPosition === null) {
            return null;
        }
        return $tokens[$nextMeaninfulTokenPosition];
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function getPreviousToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : ?\PhpCsFixer\Tokenizer\Token
    {
        $previousIndex = $index - 1;
        if (!isset($tokens[$previousIndex])) {
            return null;
        }
        return $tokens[$previousIndex];
    }
}
