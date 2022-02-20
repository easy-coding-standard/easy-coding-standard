<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\Fixer\Basic\BracesFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20220220\Symplify\PackageBuilder\ValueObject\MethodName;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLinePromotedPropertyFixer\StandaloneLinePromotedPropertyFixerTest
 */
final class StandaloneLinePromotedPropertyFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Promoted property should be on standalone line';
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder
     */
    private $blockFinder;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner
     */
    private $tokensNewliner;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner $tokensNewliner)
    {
        $this->blockFinder = $blockFinder;
        $this->tokensNewliner = $tokensNewliner;
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
        return $tokens->isAnyTokenKindsFound([\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE]);
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
            if (!$token->isGivenKind([\T_FUNCTION])) {
                continue;
            }
            $functionName = $this->getFunctionName($tokens, $position);
            if ($functionName !== \ECSPrefix20220220\Symplify\PackageBuilder\ValueObject\MethodName::CONSTRUCTOR) {
                continue;
            }
            $this->processFunction($tokens, $position);
        }
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
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
    /**
     * @param Tokens<Token> $tokens
     */
    private function processFunction(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : void
    {
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
            return;
        }
        $this->tokensNewliner->breakItems($blockInfo, $tokens, \Symplify\CodingStandard\TokenRunner\Enum\LineKind::CALLS);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getFunctionName(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : ?string
    {
        $nextToken = $this->getNextMeaningfulToken($tokens, $position);
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return null;
        }
        return $nextToken->getContent();
    }
}
