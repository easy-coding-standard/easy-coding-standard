<?php

namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\BlockFinder;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\PackageBuilder\ValueObject\MethodName;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLinePromotedPropertyFixer\StandaloneLinePromotedPropertyFixerTest
 */
final class StandaloneLinePromotedPropertyFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Promoted property should be on standalone line';

    /**
     * @var BlockFinder
     */
    private $blockFinder;

    /**
     * @var TokensNewliner
     */
    private $tokensNewliner;

    public function __construct(BlockFinder $blockFinder, TokensNewliner $tokensNewliner)
    {
        $this->blockFinder = $blockFinder;
        $this->tokensNewliner = $tokensNewliner;
    }

    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED,
            CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE,
        ]);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(SplFileInfo $splFileInfo, Tokens $tokens)
    {
        // function arguments, function call parameters, lambda use()
        for ($position = count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];

            if (! $token->isGivenKind([T_FUNCTION])) {
                continue;
            }

            $functionName = $this->getFunctionName($tokens, $position);
            if ($functionName !== MethodName::CONSTRUCTOR) {
                continue;
            }

            $this->processFunction($tokens, $position);
        }
    }

    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(public int $age, private string $name)
    {
    }
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
final class PromotedProperties
{
    public function __construct(
        public int $age,
        private string $name
    ) {
    }
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $position
     */
    private function processFunction(Tokens $tokens, $position)
    {
        $position = (int) $position;
        $blockInfo = $this->blockFinder->findInTokensByEdge($tokens, $position);
        if (! $blockInfo instanceof BlockInfo) {
            return;
        }

        $this->tokensNewliner->breakItems($blockInfo, $tokens);
    }

    /**
     * @param Tokens<Token> $tokens
     * @return string|null
     * @param int $position
     */
    private function getFunctionName(Tokens $tokens, $position)
    {
        $position = (int) $position;
        $nextToken = $this->getNextMeaningfulToken($tokens, $position);
        if (! $nextToken instanceof Token) {
            return null;
        }

        return $nextToken->getContent();
    }
}
