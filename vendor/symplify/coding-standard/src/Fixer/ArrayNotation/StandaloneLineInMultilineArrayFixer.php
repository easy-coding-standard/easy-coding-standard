<?php

namespace Symplify\CodingStandard\Fixer\ArrayNotation;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineKind;
use Symplify\CodingStandard\TokenRunner\ValueObject\TokenKinds;
use Symplify\CodingStandard\TokenRunner\Wrapper\FixerWrapper\ArrayWrapperFactory;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\ArrayNotation\StandaloneLineInMultilineArrayFixer\StandaloneLineInMultilineArrayFixerTest
 */
final class StandaloneLineInMultilineArrayFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Indexed arrays must have 1 item per line';

    /**
     * @var ArrayWrapperFactory
     */
    private $arrayWrapperFactory;

    /**
     * @var TokensNewliner
     */
    private $tokensNewliner;

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    public function __construct(
        ArrayWrapperFactory $arrayWrapperFactory,
        TokensNewliner $tokensNewliner,
        BlockFinder $blockFinder
    ) {
        $this->arrayWrapperFactory = $arrayWrapperFactory;
        $this->tokensNewliner = $tokensNewliner;
        $this->blockFinder = $blockFinder;
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
     * @see \PhpCsFixer\Fixer\ControlStructure\TrailingCommaInMultilineFixer::getPriority()
     * @return int
     */
    public function getPriority()
    {
        return 5;
    }

    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
$friends = [1 => 'Peter', 2 => 'Paul'];
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
$friends = [
    1 => 'Peter',
    2 => 'Paul'
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
        foreach ($tokens as $index => $token) {
            if (! $token->isGivenKind(TokenKinds::ARRAY_OPEN_TOKENS)) {
                continue;
            }

            $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $index);
            if (! $blockInfo instanceof BlockInfo) {
                continue;
            }

            if ($this->shouldSkipNestedArrayValue($tokens, $blockInfo)) {
                return;
            }

            $this->tokensNewliner->breakItems($blockInfo, $tokens, LineKind::ARRAYS);
        }
    }

    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    private function shouldSkipNestedArrayValue(Tokens $tokens, BlockInfo $blockInfo)
    {
        $arrayWrapper = $this->arrayWrapperFactory->createFromTokensAndBlockInfo($tokens, $blockInfo);
        if (! $arrayWrapper->isAssociativeArray()) {
            return true;
        }

        if ($arrayWrapper->getItemCount() === 1 && ! $arrayWrapper->isFirstItemArray()) {
            $previousTokenPosition = $tokens->getPrevMeaningfulToken($blockInfo->getStart());
            if ($previousTokenPosition === null) {
                return false;
            }

            /** @var Token $previousToken */
            $previousToken = $tokens[$previousTokenPosition];
            return ! $previousToken->isGivenKind(T_DOUBLE_ARROW);
        }

        return false;
    }
}
