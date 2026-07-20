<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use Override;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver;
use Symplify\CodingStandard\TokenAnalyzer\ParamNewliner;
/**
 * Every parameter of a constructor without promoted properties must be on a standalone line, unless the constructor
 * has 3 or less parameters.
 *
 * Constructors with promoted properties are handled by @see StandaloneLinePromotedPropertyFixer, so both rules can
 * be used side by side without processing the same constructor twice.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLinePlainConstructorParamFixer\StandaloneLinePlainConstructorParamFixerTest
 */
final class StandaloneLinePlainConstructorParamFixer extends AbstractSymplifyFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenAnalyzer\ParamNewliner
     */
    private $paramNewliner;
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver
     */
    private $methodNameResolver;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Constructor param should be on a standalone line to ease git diffs on new dependency';
    /**
     * Short parameter lists are readable on a single line.
     * @var int
     */
    private const MIN_PARAM_COUNT = 4;
    /**
     * @var int[]
     */
    private const PROMOTION_KINDS = [CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE];
    public function __construct(ParamNewliner $paramNewliner, MethodNameResolver $methodNameResolver)
    {
        $this->paramNewliner = $paramNewliner;
        $this->methodNameResolver = $methodNameResolver;
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\Basic\BracesFixer::getPriority()
     */
    #[Override]
    public function getPriority(): int
    {
        return 40;
    }
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
final class SomeController
{
    public function __construct(FormModel $formModel, SubmissionModel $submissionModel)
    {
    }
}
CODE_SAMPLE
)]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens): void
    {
        // from the bottom up, as adding tokens shifts every position after them
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (!$token->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            if (!$this->methodNameResolver->isMethodName($tokens, $position, '__construct')) {
                continue;
            }
            $paramBracketPosition = $this->resolveOpenBracketPosition($tokens, $position);
            if ($paramBracketPosition === null) {
                continue;
            }
            if ($this->countParams($tokens, $paramBracketPosition) < self::MIN_PARAM_COUNT) {
                continue;
            }
            if ($this->hasPromotedProperty($tokens, $paramBracketPosition)) {
                continue;
            }
            $this->paramNewliner->processFunction($tokens, $position);
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function resolveOpenBracketPosition(Tokens $tokens, int $position): ?int
    {
        $namePosition = $tokens->getNextMeaningfulToken($position);
        if ($namePosition === null) {
            return null;
        }
        return $tokens->getNextMeaningfulToken($namePosition);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function countParams(Tokens $tokens, int $openBracketPosition): int
    {
        $closeBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBracketPosition);
        $paramCount = 0;
        $nestingLevel = 0;
        for ($index = $openBracketPosition + 1; $index < $closeBracketPosition; ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if ($token->equalsAny(['(', '[', '{'])) {
                ++$nestingLevel;
                continue;
            }
            if ($token->equalsAny([')', ']', '}'])) {
                --$nestingLevel;
                continue;
            }
            if ($nestingLevel === 0 && $token->isGivenKind(\T_VARIABLE)) {
                ++$paramCount;
            }
        }
        return $paramCount;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function hasPromotedProperty(Tokens $tokens, int $openBracketPosition): bool
    {
        $closeBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBracketPosition);
        for ($index = $openBracketPosition + 1; $index < $closeBracketPosition; ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if ($token->isGivenKind(self::PROMOTION_KINDS)) {
                return \true;
            }
        }
        return \false;
    }
}
