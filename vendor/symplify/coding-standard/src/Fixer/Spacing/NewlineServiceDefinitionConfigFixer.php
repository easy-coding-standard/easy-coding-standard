<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\SymfonyClosureAnalyzer;
use Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser;
use ECSPrefix202209\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix202209\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix202209\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\NewlineServiceDefinitionConfigFixer\NewlineServiceDefinitionConfigFixerTest
 */
final class NewlineServiceDefinitionConfigFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Add newline for a fluent call on service definition in Symfony config';
    /**
     * @var string[]
     */
    private const FLUENT_METHOD_NAMES = ['call', 'property', 'args', 'arg'];
    /**
     * @var \PhpCsFixer\WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    /**
     * @var \Symplify\CodingStandard\TokenAnalyzer\SymfonyClosureAnalyzer
     */
    private $symfonyClosureAnalyzer;
    /**
     * @var \Symplify\CodingStandard\TokenRunner\Traverser\TokenReverser
     */
    private $tokenReverser;
    public function __construct(WhitespacesFixerConfig $whitespacesFixerConfig, SymfonyClosureAnalyzer $symfonyClosureAnalyzer, TokenReverser $tokenReverser)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->symfonyClosureAnalyzer = $symfonyClosureAnalyzer;
        $this->tokenReverser = $tokenReverser;
    }
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_RETURN, \T_STATIC, \T_FUNCTION, \T_VARIABLE, \T_STRING, \T_OBJECT_OPERATOR]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens) : void
    {
        if (!$this->symfonyClosureAnalyzer->isContainerConfiguratorClosure($tokens)) {
            return;
        }
        $reversedTokens = $this->tokenReverser->reverse($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind(\T_OBJECT_OPERATOR)) {
                continue;
            }
            if (!$this->isNextTokenMethodCallNamed($tokens, $index, self::FLUENT_METHOD_NAMES)) {
                continue;
            }
            $previousToken = $this->getPreviousToken($tokens, $index);
            if (!$previousToken instanceof Token) {
                continue;
            }
            if ($previousToken->isWhitespace()) {
                continue;
            }
            $newlineAndIndent = $this->whitespacesFixerConfig->getLineEnding() . \str_repeat($this->whitespacesFixerConfig->getIndent(), 2);
            $tokens->ensureWhitespaceAtIndex($index, 0, $newlineAndIndent);
        }
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class)->call('configure', [['values']]);
};
CODE_SAMPLE
, <<<'CODE_SAMPLE'
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\CodingStandard\Fixer\LineLength\LineLengthFixer;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();
    $services->set(LineLengthFixer::class)
        ->call('configure', [['values']]);
};
CODE_SAMPLE
)]);
    }
    /**
     * Must run before
     *
     * @see \PhpCsFixer\Fixer\Whitespace\MethodChainingIndentationFixer::getPriority()
     */
    public function getPriority() : int
    {
        return 39;
    }
    /**
     * @param string[] $methodNames
     * @param Tokens<Token> $tokens
     */
    private function isNextTokenMethodCallNamed(Tokens $tokens, int $index, array $methodNames) : bool
    {
        $nextToken = $this->getNextMeaningfulToken($tokens, $index);
        if (!$nextToken instanceof Token) {
            return \false;
        }
        if (!$nextToken->isGivenKind(\T_STRING)) {
            return \false;
        }
        return \in_array($nextToken->getContent(), $methodNames, \true);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getPreviousToken(Tokens $tokens, int $index) : ?Token
    {
        $previousIndex = $index - 1;
        return $tokens[$previousIndex] ?? null;
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextMeaningfulToken(Tokens $tokens, int $index) : ?Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($index);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        return $tokens[$nextMeaningfulTokenPosition];
    }
}
