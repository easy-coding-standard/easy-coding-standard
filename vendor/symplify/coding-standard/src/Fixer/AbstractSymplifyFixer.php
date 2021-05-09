<?php

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

abstract class AbstractSymplifyFixer implements FixerInterface
{
    /**
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return self::class;
    }

    /**
     * @return bool
     */
    public function isRisky()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function supports(SplFileInfo $file)
    {
        return true;
    }

    /**
     * @return mixed[]
     * @param Tokens<Token> $tokens
     */
    protected function reverseTokens(Tokens $tokens)
    {
        return array_reverse($tokens->toArray(), true);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return \PhpCsFixer\Tokenizer\Token|null
     * @param int $index
     */
    protected function getNextMeaningfulToken(Tokens $tokens, $index)
    {
        $index = (int) $index;
        $nextMeaninfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaninfulTokenPosition === null) {
            return null;
        }

        return $tokens[$nextMeaninfulTokenPosition];
    }

    /**
     * @param Tokens<Token> $tokens
     * @return \PhpCsFixer\Tokenizer\Token|null
     * @param int $index
     */
    protected function getPreviousToken(Tokens $tokens, $index)
    {
        $index = (int) $index;
        $previousIndex = $index - 1;
        if (! isset($tokens[$previousIndex])) {
            return null;
        }

        return $tokens[$previousIndex];
    }
}
