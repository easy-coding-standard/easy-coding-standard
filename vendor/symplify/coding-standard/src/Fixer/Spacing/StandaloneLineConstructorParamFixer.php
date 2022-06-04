<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver;
use Symplify\CodingStandard\TokenAnalyzer\ParamNewliner;
use ECSPrefix20220604\Symplify\PackageBuilder\ValueObject\MethodName;
use ECSPrefix20220604\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220604\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220604\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineConstructorParamFixer\StandaloneLineConstructorParamFixerTest
 */
final class StandaloneLineConstructorParamFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220604\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Constructor param should be on a standalone line to ease git diffs on new dependency';
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\ParamNewliner
     */
    private $paramNewliner;
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver
     */
    private $methodNameResolver;
    public function __construct(\Symplify\CodingStandard\TokenAnalyzer\ParamNewliner $paramNewliner, \Symplify\CodingStandard\TokenAnalyzer\Naming\MethodNameResolver $methodNameResolver)
    {
        $this->paramNewliner = $paramNewliner;
        $this->methodNameResolver = $methodNameResolver;
    }
    /**
     * Must run before
     *
     * @see BracesFixer::getPriority()
     */
    public function getPriority() : int
    {
        return 40;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (!$token->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            if (!$this->methodNameResolver->isMethodName($tokens, $position, \ECSPrefix20220604\Symplify\PackageBuilder\ValueObject\MethodName::CONSTRUCTOR)) {
                continue;
            }
            $this->paramNewliner->processFunction($tokens, $position);
        }
    }
    public function getRuleDefinition() : \ECSPrefix20220604\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220604\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220604\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(public int $age, private string $name)
    {
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(
        public int $age,
        private string $name
    ) {
    }
}
CODE_SAMPLE
)]);
    }
}
