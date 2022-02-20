<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Naming;

use ECSPrefix20220220\Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\StandardizeHereNowDocKeywordFixerTest
 */
final class StandardizeHereNowDocKeywordFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \ECSPrefix20220220\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @api
     * @var string
     */
    public const KEYWORD = 'keyword';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use configured nowdoc and heredoc keyword';
    /**
     * @api
     * @var string
     */
    private const DEFAULT_KEYWORD = 'CODE_SAMPLE';
    /**
     * @see https://regex101.com/r/ED2b9V/1
     * @var string
     */
    private const START_HEREDOC_NOWDOC_NAME_REGEX = '#(<<<(\')?)(?<name>.*?)((\')?\\s)#';
    /**
     * @var string
     */
    private $keyword = self::DEFAULT_KEYWORD;
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_START_HEREDOC, T_START_NOWDOC]);
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
            if ($token->isGivenKind(\T_START_HEREDOC)) {
                $this->fixStartToken($tokens, $token, $position);
            }
            if ($token->isGivenKind(\T_END_HEREDOC)) {
                $this->fixEndToken($tokens, $token, $position);
            }
        }
    }
    public function getRuleDefinition() : \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20220220\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
$value = <<<'WHATEVER'
...
'WHATEVER'
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$value = <<<'CODE_SNIPPET'
...
'CODE_SNIPPET'
CODE_SAMPLE
, [self::KEYWORD => 'CODE_SNIPPET'])]);
    }
    public function configure(array $configuration) : void
    {
        $this->keyword = $configuration[self::KEYWORD] ?? self::DEFAULT_KEYWORD;
    }
    public function getConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        throw new \ECSPrefix20220220\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function fixStartToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $position) : void
    {
        $match = \ECSPrefix20220220\Nette\Utils\Strings::match($token->getContent(), self::START_HEREDOC_NOWDOC_NAME_REGEX);
        if (!isset($match['name'])) {
            return;
        }
        $newContent = \ECSPrefix20220220\Nette\Utils\Strings::replace($token->getContent(), self::START_HEREDOC_NOWDOC_NAME_REGEX, '$1' . $this->keyword . '$4');
        $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $newContent]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function fixEndToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $position) : void
    {
        if ($token->getContent() === $this->keyword) {
            return;
        }
        $tokenContent = $token->getContent();
        $trimmedTokenContent = \trim($tokenContent);
        $spaceEnd = '';
        if ($tokenContent !== $trimmedTokenContent) {
            $spaceEnd = \substr($tokenContent, 0, \strlen($tokenContent) - \strlen($trimmedTokenContent));
        }
        $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $spaceEnd . $this->keyword]);
    }
}
