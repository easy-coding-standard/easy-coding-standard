<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;
final class LineLengthCloserTransformer
{
    /**
     * @var CallAnalyzer
     */
    private $callAnalyzer;
    /**
     * @var TokenFinder
     */
    private $tokenFinder;
    /**
     * @param \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer $callAnalyzer
     * @param \Symplify\CodingStandard\TokenRunner\TokenFinder $tokenFinder
     */
    public function __construct($callAnalyzer, $tokenFinder)
    {
        $this->callAnalyzer = $callAnalyzer;
        $this->tokenFinder = $tokenFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @param int $kind
     * @param string $newlineIndentWhitespace
     * @param string $closingBracketNewlineIndentWhitespace
     */
    public function insertNewlineBeforeClosingIfNeeded($tokens, $blockInfo, $kind, $newlineIndentWhitespace, $closingBracketNewlineIndentWhitespace)
    {
        $isMethodCall = $this->callAnalyzer->isMethodCall($tokens, $blockInfo->getStart());
        $endIndex = $blockInfo->getEnd();
        $previousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $endIndex);
        $previousPreviousToken = $this->tokenFinder->getPreviousMeaningfulToken($tokens, $previousToken);
        // special case, if the function is followed by array - method([...]) - but not - method([[...]]))
        if ($this->shouldAddNewlineEarlier($previousToken, $previousPreviousToken, $isMethodCall, $kind)) {
            $tokens->ensureWhitespaceAtIndex($endIndex - 1, 0, $newlineIndentWhitespace);
            return;
        }
        $tokens->ensureWhitespaceAtIndex($endIndex - 1, 1, $closingBracketNewlineIndentWhitespace);
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Token $previousToken
     * @param \PhpCsFixer\Tokenizer\Token $previousPreviousToken
     * @param bool $isMethodCall
     * @param int $kind
     * @return bool
     */
    private function shouldAddNewlineEarlier($previousToken, $previousPreviousToken, $isMethodCall, $kind)
    {
        if ($isMethodCall) {
            return \false;
        }
        if ($kind !== \Symplify\CodingStandard\TokenRunner\ValueObject\LineKind::CALLS) {
            return \false;
        }
        if (!$previousToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return \false;
        }
        return !$previousPreviousToken->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
}
