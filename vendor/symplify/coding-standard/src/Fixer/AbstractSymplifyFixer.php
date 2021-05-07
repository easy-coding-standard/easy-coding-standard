<?php

namespace Symplify\CodingStandard\Fixer;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
abstract class AbstractSymplifyFixer implements \PhpCsFixer\Fixer\FixerInterface
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
        return \false;
    }
    /**
     * @param \SplFileInfo $file
     * @return bool
     */
    public function supports($file)
    {
        return \true;
    }
    /**
     * @return mixed[]
     * @param Tokens<Token> $tokens
     */
    protected function reverseTokens($tokens)
    {
        return \array_reverse($tokens->toArray(), \true);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return \PhpCsFixer\Tokenizer\Token|null
     * @param int $index
     */
    protected function getNextMeaningfulToken($tokens, $index)
    {
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
    protected function getPreviousToken($tokens, $index)
    {
        $previousIndex = $index - 1;
        if (!isset($tokens[$previousIndex])) {
            return null;
        }
        return $tokens[$previousIndex];
    }
}
