<?php

namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\WhitespacesFixerConfig;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\TokenAnalyzer\SymfonyClosureAnalyzer;
use ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\NewlineServiceDefinitionConfigFixer\NewlineServiceDefinitionConfigFixerTest
 */
final class NewlineServiceDefinitionConfigFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20210516\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Add newline for a fluent call on service definition in Symfony config';
    /**
     * @var string[]
     */
    const FLUENT_METHOD_NAMES = ['call', 'property', 'args', 'arg'];
    /**
     * @var WhitespacesFixerConfig
     */
    private $whitespacesFixerConfig;
    /**
     * @var SymfonyClosureAnalyzer
     */
    private $symfonyClosureAnalyzer;
    public function __construct(\PhpCsFixer\WhitespacesFixerConfig $whitespacesFixerConfig, \Symplify\CodingStandard\TokenAnalyzer\SymfonyClosureAnalyzer $symfonyClosureAnalyzer)
    {
        $this->whitespacesFixerConfig = $whitespacesFixerConfig;
        $this->symfonyClosureAnalyzer = $symfonyClosureAnalyzer;
    }
    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_RETURN, \T_STATIC, \T_FUNCTION, \T_VARIABLE, \T_STRING, \T_OBJECT_OPERATOR]);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
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
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new \ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
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
     * @return int
     */
    public function getPriority()
    {
        return 39;
    }
    /**
     * @param string[] $methodNames
     * @param Tokens<Token> $tokens
     * @param int $index
     * @return bool
     */
    private function isNextTokenMethodCallNamed(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, array $methodNames)
    {
        $index = (int) $index;
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
