<?php

namespace Symplify\CodingStandard\Fixer\LineLength;

use ECSPrefix20210510\Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\FunctionCallNameMatcher;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\LineLengthFixerTest
 * @see \Symplify\CodingStandard\Tests\Fixer\LineLength\LineLengthFixer\ConfiguredLineLengthFixerTest
 */
final class LineLengthFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface, \Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @api
     * @var string
     */
    const LINE_LENGTH = 'line_length';
    /**
     * @api
     * @var string
     */
    const BREAK_LONG_LINES = 'break_long_lines';
    /**
     * @api
     * @var string
     */
    const INLINE_SHORT_LINES = 'inline_short_lines';
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Array items, method parameters, method call arguments, new arguments should be on same/standalone line to fit line length.';
    /**
     * @var int
     */
    const DEFAULT_LINE_LENGHT = 120;
    /**
     * @var int
     */
    private $lineLength = self::DEFAULT_LINE_LENGHT;
    /**
     * @var bool
     */
    private $breakLongLines = \true;
    /**
     * @var bool
     */
    private $inlineShortLines = \true;
    /**
     * @var LineLengthTransformer
     */
    private $lineLengthTransformer;
    /**
     * @var BlockFinder
     */
    private $blockFinder;
    /**
     * @var FunctionCallNameMatcher
     */
    private $functionCallNameMatcher;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\LineLengthTransformer $lineLengthTransformer, \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder, \Symplify\CodingStandard\TokenAnalyzer\FunctionCallNameMatcher $functionCallNameMatcher)
    {
        $this->lineLengthTransformer = $lineLengthTransformer;
        $this->blockFinder = $blockFinder;
        $this->functionCallNameMatcher = $functionCallNameMatcher;
    }
    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([
            // "["
            \T_ARRAY,
            // "array"()
            \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN,
            '(',
            ')',
            // "function"
            \T_FUNCTION,
            // "use" (...)
            \PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA,
            // "new"
            \T_NEW,
        ]);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        // function arguments, function call parameters, lambda use()
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if ($token->equals(')')) {
                $this->processMethodCall($tokens, $position);
                continue;
            }
            // opener
            if ($token->isGivenKind([\T_FUNCTION, \PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA, \T_NEW])) {
                $this->processFunctionOrArray($tokens, $position);
                continue;
            }
            // closer
            if ($token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE) || $token->equals(')') && $token->isArray()) {
                $this->processFunctionOrArray($tokens, $position);
            }
        }
    }
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new \Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
function some($veryLong, $superLong, $oneMoreTime)
{
}

function another(
    $short,
    $now
) {
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
function some(
    $veryLong,
    $superLong,
    $oneMoreTime
) {
}

function another($short, $now) {
}
CODE_SAMPLE
, [self::LINE_LENGTH => 40])]);
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\ArrayNotation\TrimArraySpacesFixer
     * @return int
     */
    public function getPriority()
    {
        return 5;
    }
    /**
     * @param mixed[]|null $configuration
     * @return void
     */
    public function configure($configuration = null)
    {
        $this->lineLength = isset($configuration[self::LINE_LENGTH]) ? $configuration[self::LINE_LENGTH] : self::DEFAULT_LINE_LENGHT;
        $this->breakLongLines = isset($configuration[self::BREAK_LONG_LINES]) ? $configuration[self::BREAK_LONG_LINES] : \true;
        $this->inlineShortLines = isset($configuration[self::INLINE_SHORT_LINES]) ? $configuration[self::INLINE_SHORT_LINES] : \true;
    }
    /**
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    public function getConfigurationDefinition()
    {
        throw new \Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $position
     */
    private function processMethodCall(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $position = (int) $position;
        $methodNamePosition = $this->functionCallNameMatcher->matchName($tokens, $position);
        if ($methodNamePosition === null) {
            return;
        }
        $blockInfo = $this->blockFinder->findInTokensByPositionAndContent($tokens, $methodNamePosition, '(');
        if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
            return;
        }
        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        $comments = $tokens->findGivenKind(\T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd());
        if ($comments !== []) {
            return;
        }
        $this->lineLengthTransformer->fixStartPositionToEndPosition($blockInfo, $tokens, $this->lineLength, $this->breakLongLines, $this->inlineShortLines);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $position
     */
    private function processFunctionOrArray(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $position = (int) $position;
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
            return;
        }
        if ($this->shouldSkip($tokens, $blockInfo)) {
            return;
        }
        $this->lineLengthTransformer->fixStartPositionToEndPosition($blockInfo, $tokens, $this->lineLength, $this->breakLongLines, $this->inlineShortLines);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    private function shouldSkip(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo)
    {
        // no items inside => skip
        if ($blockInfo->getEnd() - $blockInfo->getStart() <= 1) {
            return \true;
        }
        if ($this->isHerenowDoc($tokens, $blockInfo)) {
            return \true;
        }
        // is array with indexed values "=>"
        $doubleArrowTokens = $tokens->findGivenKind(\T_DOUBLE_ARROW, $blockInfo->getStart(), $blockInfo->getEnd());
        if ($doubleArrowTokens !== []) {
            return \true;
        }
        // has comments => dangerous to change: https://github.com/symplify/symplify/issues/973
        return (bool) $tokens->findGivenKind(\T_COMMENT, $blockInfo->getStart(), $blockInfo->getEnd());
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    private function isHerenowDoc(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo)
    {
        // heredoc/nowdoc => skip
        $nextToken = $this->getNextMeaningfulToken($tokens, $blockInfo->getStart());
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return \false;
        }
        return \ECSPrefix20210510\Nette\Utils\Strings::contains($nextToken->getContent(), '<<<');
    }
}
