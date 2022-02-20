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
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\NewlineServiceDefinitionConfigFixer\NewlineServiceDefinitionConfigFixerTest
 */
final class NewlineServiceDefinitionConfigFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
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
    public function __construct(\PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig, \Symplify\CodingStandard\TokenAnalyzer\SymfonyClosureAnalyzer $symfonyClosureAnalyzer)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->symfonyClosureAnalyzer = $symfonyClosureAnalyzer;
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
        return $tokens->isAllTokenKindsFound([\T_RETURN, \T_STATIC, \T_FUNCTION, \T_VARIABLE, \T_STRING, \T_OBJECT_OPERATOR]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        if (!$this->symfonyClosureAnalyzer->isContainerConfiguratorClosure($tokens)) {
            return;
        }
        $reversedTokens = $this->reverseTokens($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind(\T_OBJECT_OPERATOR)) {
                continue;
            }
            if (!$this->isNextTokenMethodCallNamed($tokens, $index, self::FLUENT_METHOD_NAMES)) {
                continue;
            }
            $previousToken = $this->getPreviousToken($tokens, $index);
            if (!$previousToken instanceof \PhpCsFixer\Tokenizer\Token) {
                continue;
            }
            if ($previousToken->isWhitespace()) {
                continue;
            }
            $newlineAndIndent = $this->whitespacesFixerConfig->getLineEnding() . \str_repeat($this->whitespacesFixerConfig->getIndent(), 2);
            $tokens->ensureWhitespaceAtIndex($index, 0, $newlineAndIndent);
        }
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
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
    private function isNextTokenMethodCallNamed(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, array $methodNames) : bool
    {
        $nextToken = $this->getNextMeaningfulToken($tokens, $index);
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return \false;
        }
        if (!$nextToken->isGivenKind(\T_STRING)) {
            return \false;
        }
        return \in_array($nextToken->getContent(), $methodNames, \true);
    }
}
