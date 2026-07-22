<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use Override;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenRunner\Enum\LineKind;
use Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
/**
 * Every argument of a Symfony attribute must be on a standalone line, to ease git diffs when arguments change.
 *
 * Only a fixed allowlist of Symfony attributes is handled, so third-party attributes keep their original layout.
 * Attributes are matched by their short name (#[AsCommand] or #[\Symfony\...\AsCommand]). See self::SYMFONY_ATTRIBUTE_SHORT_NAMES.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\StandaloneLineSymfonyAttributeParamFixer\StandaloneLineSymfonyAttributeParamFixerTest
 */
final class StandaloneLineSymfonyAttributeParamFixer extends AbstractSymplifyFixer
{
    /**
     * @readonly
     * @var \Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer\TokensNewliner
     */
    private $tokensNewliner;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Symfony attribute argument should be on a standalone line to ease git diffs on change';
    /**
     * @var string[]
     */
    private const SYMFONY_ATTRIBUTE_SHORT_NAMES = ['AsCommand', 'Route', 'Autowire', 'AutowireIterator', 'AutowireLocator', 'AsAlias', 'AsDecorator', 'AsTaggedItem', 'When', 'AsEventListener', 'AsMessageHandler', 'AsController', 'MapRequestPayload', 'MapQueryParameter', 'MapQueryString', 'MapEntity', 'IsGranted'];
    public function __construct(TokensNewliner $tokensNewliner)
    {
        $this->tokensNewliner = $tokensNewliner;
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
#[\Symfony\Component\Console\Attribute\AsCommand(name: 'app:some', description: 'Some description')]
final class SomeCommand
{
}
CODE_SAMPLE
)]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens): bool
    {
        return $tokens->isTokenKindFound(\T_ATTRIBUTE);
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
            if (!$token->isGivenKind(\T_ATTRIBUTE)) {
                continue;
            }
            $attributeEndPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_ATTRIBUTE, $position);
            // attribute without arguments, e.g. #[Override]; the next "(" belongs to a later statement
            $openBracketPosition = $tokens->getNextTokenOfKind($position, ['(']);
            if ($openBracketPosition === null) {
                continue;
            }
            if ($openBracketPosition > $attributeEndPosition) {
                continue;
            }
            if (!$this->isSymfonyAttribute($tokens, $openBracketPosition)) {
                continue;
            }
            $closeBracketPosition = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openBracketPosition);
            if ($tokens->getNextMeaningfulToken($openBracketPosition) === $closeBracketPosition) {
                // empty argument list, e.g. #[\Symfony\...\AsCommand()]
                continue;
            }
            $blockInfo = new BlockInfo($openBracketPosition, $closeBracketPosition);
            $this->tokensNewliner->breakItems($blockInfo, $tokens, LineKind::CALLS);
        }
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isSymfonyAttribute(Tokens $tokens, int $openBracketPosition): bool
    {
        // the attribute short name is the T_STRING right before its "(", e.g. "AsCommand"
        $previousPosition = $tokens->getPrevMeaningfulToken($openBracketPosition);
        if ($previousPosition === null) {
            return \false;
        }
        /** @var Token $previousToken */
        $previousToken = $tokens[$previousPosition];
        if (!$previousToken->isGivenKind(\T_STRING)) {
            return \false;
        }
        return in_array($previousToken->getContent(), self::SYMFONY_ATTRIBUTE_SHORT_NAMES, \true);
    }
}
