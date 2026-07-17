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
use Symplify\CodingStandard\TokenAnalyzer\ParamNewliner;
/**
 * Every parameter of a public method marked with #[Required] or @required must be on a standalone line.
 *
 * These methods are dependency injection points. Their parameter list grows over time and a one-line signature
 * makes every added or removed dependency a full-line diff. One parameter per line keeps the diff at the single
 * dependency that changed.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineRequiredParamFixer\StandaloneLineRequiredParamFixerTest
 */
final class StandaloneLineRequiredParamFixer extends AbstractSymplifyFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenAnalyzer\ParamNewliner
     */
    private $paramNewliner;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Parameter of a #[Required] method should be on a standalone line to ease git diffs on new dependency';
    /**
     * @var int[]
     */
    private const METHOD_HEAD_KINDS = [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT, \T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_STATIC, \T_FINAL, \T_ABSTRACT, \T_READONLY];
    public function __construct(ParamNewliner $paramNewliner)
    {
        $this->paramNewliner = $paramNewliner;
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
    #[Required]
    public function autowireDependencies(FormModel $formModel, SubmissionModel $submissionModel): void
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
            if (!$this->isPublicRequiredMethod($tokens, $position)) {
                continue;
            }
            if (!$this->hasParams($tokens, $position)) {
                continue;
            }
            $this->paramNewliner->processFunction($tokens, $position);
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isPublicRequiredMethod(Tokens $tokens, int $position): bool
    {
        $headStart = $this->resolveMethodHeadStart($tokens, $position);
        $isPublic = \false;
        $isRequired = \false;
        for ($index = $headStart; $index < $position; ++$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if ($token->isGivenKind(\T_PUBLIC)) {
                $isPublic = \true;
                continue;
            }
            // within the method head, any "Required" bareword can only come from a #[Required] attribute
            if ($token->isGivenKind(\T_STRING) && $token->getContent() === 'Required') {
                $isRequired = \true;
                continue;
            }
            if ($token->isGivenKind(\T_DOC_COMMENT) && $this->hasRequiredAnnotation($token->getContent())) {
                $isRequired = \true;
            }
        }
        return $isPublic && $isRequired;
    }
    /**
     * Walk back over modifiers, doc blocks and attributes to the first token of the method head.
     *
     * @param Tokens<Token> $tokens
     */
    private function resolveMethodHeadStart(Tokens $tokens, int $position): int
    {
        $headStart = $position;
        for ($index = $position - 1; $index >= 0; --$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if ($token->isGivenKind(self::METHOD_HEAD_KINDS)) {
                $headStart = $index;
                continue;
            }
            if ($token->isGivenKind(CT::T_ATTRIBUTE_CLOSE)) {
                $index = $tokens->findBlockStart(Tokens::BLOCK_TYPE_ATTRIBUTE, $index);
                $headStart = $index;
                continue;
            }
            break;
        }
        return $headStart;
    }
    private function hasRequiredAnnotation(string $docComment): bool
    {
        return (bool) preg_match('#@required\b#i', $docComment);
    }
    /**
     * An empty parameter list would be broken into an empty line between the brackets.
     *
     * @param Tokens<Token> $tokens
     */
    private function hasParams(Tokens $tokens, int $position): bool
    {
        $namePosition = $tokens->getNextMeaningfulToken($position);
        if ($namePosition === null) {
            return \false;
        }
        $openBracketPosition = $tokens->getNextMeaningfulToken($namePosition);
        if ($openBracketPosition === null) {
            return \false;
        }
        $firstParamPosition = $tokens->getNextMeaningfulToken($openBracketPosition);
        if ($firstParamPosition === null) {
            return \false;
        }
        /** @var Token $firstParamToken */
        $firstParamToken = $tokens[$firstParamPosition];
        return $firstParamToken->getContent() !== ')';
    }
}
