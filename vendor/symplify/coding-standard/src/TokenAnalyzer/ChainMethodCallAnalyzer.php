<?php

namespace Symplify\CodingStandard\TokenAnalyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class ChainMethodCallAnalyzer
{
    /**
     * @var NewlineAnalyzer
     */
    private $newlineAnalyzer;
    /**
     * @var int
     */
    private $bracketNesting = 0;
    public function __construct(\Symplify\CodingStandard\TokenAnalyzer\NewlineAnalyzer $newlineAnalyzer)
    {
        $this->newlineAnalyzer = $newlineAnalyzer;
    }
    /**
     * Matches e.g: return app()->some(), app()->some(), (clone app)->some()
     *
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return bool
     */
    public function isPreceededByFuncCall(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        for ($i = $position; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];
            if ($currentToken->getContent() === 'clone') {
                return \true;
            }
            if ($currentToken->getContent() === '(') {
                return $this->newlineAnalyzer->doesContentBeforeBracketRequireNewline($tokens, $i);
            }
            if ($this->newlineAnalyzer->isNewlineToken($currentToken)) {
                return \false;
            }
        }
        return \false;
    }
    /**
     * Matches e.g. someMethod($this->some()->method()), [$this->some()->method()]
     *
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return bool
     */
    public function isPartOfMethodCallOrArray(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $this->bracketNesting = 0;
        for ($i = $position; $i >= 0; --$i) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$i];
            // break
            if ($this->newlineAnalyzer->isNewlineToken($currentToken)) {
                return \false;
            }
            if ($this->isBreakingChar($currentToken)) {
                return \true;
            }
            if ($this->shouldBreakOnBracket($currentToken)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return bool
     */
    private function isBreakingChar(\PhpCsFixer\Tokenizer\Token $currentToken)
    {
        if ($currentToken->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, \T_ARRAY, \T_DOUBLE_COLON])) {
            return \true;
        }
        if ($currentToken->getContent() === '[') {
            return \true;
        }
        return $currentToken->getContent() === '.';
    }
    /**
     * @return bool
     */
    private function shouldBreakOnBracket(\PhpCsFixer\Tokenizer\Token $token)
    {
        if ($token->getContent() === ')') {
            --$this->bracketNesting;
            return \false;
        }
        if ($token->getContent() === '(') {
            if ($this->bracketNesting !== 0) {
                ++$this->bracketNesting;
                return \false;
            }
            return \true;
        }
        return \false;
    }
}
