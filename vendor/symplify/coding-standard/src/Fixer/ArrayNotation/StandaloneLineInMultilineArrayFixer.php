<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\StandaloneLineInMultilineArrayFixerTest
 */
final class StandaloneLineInMultilineArrayFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Indexed arrays must have 1 item per line';
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory
     */
    private $arrayWrapperFactory;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner
     */
    private $tokensNewliner;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder
     */
    private $blockFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory $arrayWrapperFactory, \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner $tokensNewliner, \Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder $blockFinder)
    {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->tokensNewliner = $tokensNewliner;
        $this->blockFinder = $blockFinder;
    }
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::getPriority()
     */
    public function getPriority() : int
    {
        return 5;
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
$friends = [1 => 'Peter', 2 => 'Paul'];
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$friends = [
    1 => 'Peter',
    2 => 'Paul'
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
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds::ARRAY_OPEN_TOKENS)) {
                continue;
            }
            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if (!$blockInfo instanceof \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo) {
                continue;
            }
            if ($this->shouldSkipNestedArrayValue($tokens, $blockInfo)) {
                return;
            }
            $this->tokensNewliner->breakItems($blockInfo, $tokens, \Symplify\CodingStandard\TokenRunner\Enum\LineKind::ARRAYS);
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function shouldSkipNestedArrayValue(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo) : bool
    {
        $arrayWrapper = $this->arrayWrapperFactory->createFromTokensAndBlockInfo($tokens, $blockInfo);
        if (!$arrayWrapper->isAssociativeArray()) {
            return \true;
        }
        if ($arrayWrapper->getItemCount() === 1 && !$arrayWrapper->isFirstItemArray()) {
            $previousTokenPosition = $tokens->getPrevMeaningfulToken($blockInfo->getStart());
            if ($previousTokenPosition === null) {
                return \false;
            }
            /** @var Token $previousToken */
            $previousToken = $tokens[$previousTokenPosition];
            return !$previousToken->isGivenKind(\T_DOUBLE_ARROW);
        }
        return \false;
    }
}
