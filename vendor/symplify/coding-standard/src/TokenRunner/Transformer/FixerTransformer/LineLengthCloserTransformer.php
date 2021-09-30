<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer;
use Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
final class LineLengthCloserTransformer
{
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer
     */
    private $callAnalyzer;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\TokenFinder
     */
    private $tokenFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\CallAnalyzer $callAnalyzer, \Symplify\CodingStandard\TokenRunner\TokenFinder $tokenFinder)
    {
        $this->callAnalyzer = $callAnalyzer;
        $this->tokenFinder = $tokenFinder;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function insertNewlineBeforeClosingIfNeeded(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, int $kind, string $newlineIndentWhitespace, string $closingBracketNewlineIndentWhitespace) : void
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
    private function shouldAddNewlineEarlier(\PhpCsFixer\Tokenizer\Token $previousToken, \PhpCsFixer\Tokenizer\Token $previousPreviousToken, bool $isMethodCall, int $kind) : bool
    {
        if ($isMethodCall) {
            return \false;
        }
        if ($kind !== \Symplify\CodingStandard\TokenRunner\Enum\LineKind::CALLS) {
            return \false;
        }
        if (!$previousToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE)) {
            return \false;
        }
        return !$previousPreviousToken->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
}
