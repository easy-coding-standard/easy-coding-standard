<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\ArrayNotation;

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
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\ArrayOpenerAndCloserNewlineFixer\ArrayOpenerAndCloserNewlineFixerTest
 */
final class ArrayOpenerAndCloserNewlineFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Indexed PHP array opener [ and closer ] must be on own line';
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder
     */
    private $arrayBlockInfoFinder;
    /**
     * @var \PhpCsFixer\WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer
     */
    private $arrayAnalyzer;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Traverser\ArrayBlockInfoFinder $arrayBlockInfoFinder, \PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig, \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\ArrayAnalyzer $arrayAnalyzer)
    {
        $this->arrayBlockInfoFinder = $arrayBlockInfoFinder;
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->arrayAnalyzer = $arrayAnalyzer;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\Whitespace\ArrayIndentationFixer::getPriority()
     */
    public function getPriority() : int
    {
        return 34;
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
$items = [1 => 'Hey'];
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$items = [
1 => 'Hey'
];
CODE_SAMPLE
)]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        if (!$tokens->isAnyTokenKindsFound(\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds::ARRAY_OPEN_TOKENS)) {
            return \false;
        }
        return $tokens->isTokenKindFound(\T_DOUBLE_ARROW);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $blockInfos = $this->arrayBlockInfoFinder->findArrayOpenerBlockInfos($tokens);
        foreach ($blockInfos as $blockInfo) {
            $this->fixArrayOpener($tokens, $blockInfo);
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function fixArrayOpener(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo) : void
    {
        if ($this->isNextTokenAlsoArrayOpener($tokens, $blockInfo->getStart())) {
            return;
        }
        // no items
        $itemCount = $this->arrayAnalyzer->getItemCount($tokens, $blockInfo);
        if ($itemCount === 0) {
            return;
        }
        if (!$this->arrayAnalyzer->isIndexedList($tokens, $blockInfo)) {
            return;
        }
        // closer must run before the opener, as tokens as added by traversing up
        $this->handleArrayCloser($tokens, $blockInfo->getEnd());
        $this->handleArrayOpener($tokens, $blockInfo->getStart());
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isNextTokenAlsoArrayOpener(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $nextToken = $this->getNextMeaningfulToken($tokens, $index);
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return \false;
        }
        return $nextToken->isGivenKind(\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds::ARRAY_OPEN_TOKENS);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function handleArrayCloser(\PhpCsFixer\Tokenizer\Tokens $tokens, int $arrayCloserPosition) : void
    {
        $preArrayCloserPosition = $arrayCloserPosition - 1;
        $previousCloserToken = $tokens[$preArrayCloserPosition] ?? null;
        if (!$previousCloserToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return;
        }
        // already whitespace
        if (\strpos($previousCloserToken->getContent(), "\n") !== \false) {
            return;
        }
        $tokens->ensureWhitespaceAtIndex($preArrayCloserPosition, 1, $this->whitespacesFixerConfig->getLineEnding());
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function handleArrayOpener(\PhpCsFixer\Tokenizer\Tokens $tokens, int $arrayOpenerPosition) : void
    {
        $postArrayOpenerPosition = $arrayOpenerPosition + 1;
        $nextToken = $tokens[$postArrayOpenerPosition] ?? null;
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return;
        }
        // already is whitespace
        if (\strpos($nextToken->getContent(), "\n") !== \false) {
            return;
        }
        $tokens->ensureWhitespaceAtIndex($postArrayOpenerPosition, 0, $this->whitespacesFixerConfig->getLineEnding());
    }
}
