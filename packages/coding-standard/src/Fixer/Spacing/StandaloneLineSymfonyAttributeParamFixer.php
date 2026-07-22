<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use Override;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
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
 * Both fully-qualified names (#[\Symfony\...\AsCommand]) and short names imported via a use statement (#[AsCommand])
 * are recognized. See self::SYMFONY_ATTRIBUTE_CLASSES.
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
     * @readonly
     * @var \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer
     */
    private $namespaceUsesAnalyzer;
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Symfony attribute argument should be on a standalone line to ease git diffs on change';
    /**
     * @var string[]
     */
    private const SYMFONY_ATTRIBUTE_CLASSES = ['ECSPrefix202607\Symfony\Component\Console\Attribute\AsCommand', 'ECSPrefix202607\Symfony\Component\Routing\Attribute\Route', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\Autowire', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\AutowireIterator', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\AutowireLocator', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\AsAlias', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\AsDecorator', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\AsTaggedItem', 'ECSPrefix202607\Symfony\Component\DependencyInjection\Attribute\When', 'ECSPrefix202607\Symfony\Component\EventDispatcher\Attribute\AsEventListener', 'ECSPrefix202607\Symfony\Component\Messenger\Attribute\AsMessageHandler', 'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\AsController', 'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\MapRequestPayload', 'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\MapQueryParameter', 'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\MapQueryString', 'ECSPrefix202607\Symfony\Component\HttpKernel\Attribute\MapEntity', 'ECSPrefix202607\Symfony\Component\Security\Http\Attribute\IsGranted'];
    public function __construct(TokensNewliner $tokensNewliner, NamespaceUsesAnalyzer $namespaceUsesAnalyzer)
    {
        $this->tokensNewliner = $tokensNewliner;
        $this->namespaceUsesAnalyzer = $namespaceUsesAnalyzer;
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
        $shortNameToFullName = $this->resolveShortNameToFullName($tokens);
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
            if (!$this->isSymfonyAttribute($tokens, $openBracketPosition, $shortNameToFullName)) {
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
     * @param array<string, string> $shortNameToFullName
     */
    private function isSymfonyAttribute(Tokens $tokens, int $openBracketPosition, array $shortNameToFullName): bool
    {
        $attributeName = $this->resolveAttributeName($tokens, $openBracketPosition);
        // fully-qualified name, e.g. #[\Symfony\...\AsCommand]
        if (strncmp($attributeName, '\\', strlen('\\')) === 0) {
            return in_array(ltrim($attributeName, '\\'), self::SYMFONY_ATTRIBUTE_CLASSES, \true);
        }
        // short name imported via a use statement, e.g. #[AsCommand] with "use Symfony\...\AsCommand;"
        $firstNamePart = explode('\\', $attributeName)[0];
        $fullName = $shortNameToFullName[$firstNamePart] ?? null;
        return $fullName !== null && in_array($fullName, self::SYMFONY_ATTRIBUTE_CLASSES, \true);
    }
    /**
     * Reads the attribute name written right before its "(", e.g. "\Symfony\...\AsCommand" or "AsCommand".
     *
     * @param Tokens<Token> $tokens
     */
    private function resolveAttributeName(Tokens $tokens, int $openBracketPosition): string
    {
        $attributeName = '';
        for ($index = $openBracketPosition - 1; $index >= 0; --$index) {
            /** @var Token $token */
            $token = $tokens[$index];
            if (!$token->isGivenKind([\T_STRING, \T_NS_SEPARATOR])) {
                break;
            }
            $attributeName = $token->getContent() . $attributeName;
        }
        return $attributeName;
    }
    /**
     * @param Tokens<Token> $tokens
     * @return array<string, string>
     */
    private function resolveShortNameToFullName(Tokens $tokens): array
    {
        $shortNameToFullName = [];
        foreach ($this->namespaceUsesAnalyzer->getDeclarationsFromTokens($tokens) as $namespaceUseAnalysis) {
            $shortNameToFullName[$namespaceUseAnalysis->getShortName()] = $namespaceUseAnalysis->getFullName();
        }
        return $shortNameToFullName;
    }
}
