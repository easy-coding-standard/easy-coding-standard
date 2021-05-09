<?php

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer;
use Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\ArrayOpenerAndCloserNewlineFixerTest
 */
final class ArrayOpenerAndCloserNewlineFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Indexed PHP array opener [ and closer ] must be on own line';

    /**
     * @var ArrayBlockInfoFinder
     */
    private $arrayBlockInfoFinder;

    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;

    /**
     * @var ArrayAnalyzer
     */
    private $arrayAnalyzer;

    public function __construct(
        ArrayBlockInfoFinder $arrayBlockInfoFinder,
        WhitespacesFixerConfig $whitespacesFixerConfig,
        ArrayAnalyzer $arrayAnalyzer
    ) {
        $this->arrayBlockInfoFinder = $arrayBlockInfoFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->arrayAnalyzer = $arrayAnalyzer;
    }

    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer::getPriority()
     * @return int
     */
    public function getPriority()
    {
        return 34;
    }

    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$items = [1 => 'Hey'];
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$items = [
1 => 'Hey'
];
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isCandidate(Tokens $tokens)
    {
        if (! $tokens->isAnyTokenKindsFound(TokenKinds::ARRAY_OPEN_TOKENS)) {
            return false;
        }

        return $tokens->isTokenKindFound(T_DOUBLE_ARROW);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens)
    {
        $blockInfos = $this->arrayBlockInfoFinder->findArrayOpenerBlockInfos($tokens);

        foreach ($blockInfos as $blockInfo) {
            $this->fixArrayOpener($tokens, $blockInfo);
        }
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    private function fixArrayOpener(Tokens $tokens, BlockInfo $blockInfo)
    {
        if ($this->isNextTokenAlsoArrayOpener($tokens, $blockInfo->getStart())) {
            return;
        }

        // no items
        $itemCount = $this->arrayAnalyzer->getItemCount($tokens, $blockInfo);
        if ($itemCount === 0) {
            return;
        }

        if (! $this->arrayAnalyzer->isIndexedList($tokens, $blockInfo)) {
            return;
        }

        // closer must run before the opener, as tokens as added by traversing up
        $this->handleArrayCloser($tokens, $blockInfo->getEnd());
        $this->handleArrayOpener($tokens, $blockInfo->getStart());
    }

    /**
     * @param Tokens<Token> $tokens
     * @param int $index
     * @return bool
     */
    private function isNextTokenAlsoArrayOpener(Tokens $tokens, $index)
    {
        $index = (int) $index;
        $nextToken = $this->getNextMeaningfulToken($tokens, $index);
        if (! $nextToken instanceof Token) {
            return false;
        }

        return $nextToken->isGivenKind(TokenKinds::ARRAY_OPEN_TOKENS);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $arrayCloserPosition
     */
    private function handleArrayCloser(Tokens $tokens, $arrayCloserPosition)
    {
        $arrayCloserPosition = (int) $arrayCloserPosition;
        $preArrayCloserPosition = $arrayCloserPosition - 1;

        $previousCloserToken = isset($tokens[$preArrayCloserPosition]) ? $tokens[$preArrayCloserPosition] : null;
        if (! $previousCloserToken instanceof Token) {
            return;
        }

        // already whitespace
        if (Strings::contains($previousCloserToken->getContent(), "\n")) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($preArrayCloserPosition, 1, $this->whitespacesFixerConfig->getLineEnding());
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $arrayOpenerPosition
     */
    private function handleArrayOpener(Tokens $tokens, $arrayOpenerPosition)
    {
        $arrayOpenerPosition = (int) $arrayOpenerPosition;
        $postArrayOpenerPosition = $arrayOpenerPosition + 1;

        $nextToken = isset($tokens[$postArrayOpenerPosition]) ? $tokens[$postArrayOpenerPosition] : null;
        if (! $nextToken instanceof Token) {
            return;
        }

        // already is whitespace
        if (Strings::contains($nextToken->getContent(), "\n")) {
            return;
        }

        $tokens->ensureWhitespaceAtIndex($postArrayOpenerPosition, 0, $this->whitespacesFixerConfig->getLineEnding());
    }
}
