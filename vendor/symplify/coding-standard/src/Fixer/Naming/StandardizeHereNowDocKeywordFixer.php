<?php

namespace Symplify\CodingStandard\Fixer\Naming;

use ECSPrefix20210517\Nette\Utils\Strings;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix20210517\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface;
use ECSPrefix20210517\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20210517\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample;
use ECSPrefix20210517\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
use ECSPrefix20210517\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Naming\StandardizeHereNowDocKeywordFixer\StandardizeHereNowDocKeywordFixerTest
 */
final class StandardizeHereNowDocKeywordFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20210517\Symplify\RuleDocGenerator\Contract\ConfigurableRuleInterface, \ECSPrefix20210517\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @api
     * @var string
     */
    const KEYWORD = 'keyword';
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Use configured nowdoc and heredoc keyword';
    /**
     * @api
     * @var string
     */
    const DEFAULT_KEYWORD = 'CODE_SAMPLE';
    /**
     * @see https://regex101.com/r/ED2b9V/1
     * @var string
     */
    const START_HEREDOC_NOWDOC_NAME_REGEX = '#(<<<(\')?)(?<name>.*?)((\')?\\s)#';
    /**
     * @var string
     */
    private $keyword = self::DEFAULT_KEYWORD;
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
        return $tokens->isAnyTokenKindsFound([\T_START_HEREDOC, T_START_NOWDOC]);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
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
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new \ECSPrefix20210517\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20210517\Symplify\RuleDocGenerator\ValueObject\CodeSample\ConfiguredCodeSample(<<<'CODE_SAMPLE'
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
    /**
     * @param mixed[]|null $configuration
     * @return void
     */
    public function configure($configuration = null)
    {
        $this->keyword = isset($configuration[self::KEYWORD]) ? $configuration[self::KEYWORD] : self::DEFAULT_KEYWORD;
    }
    /**
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    public function getConfigurationDefinition()
    {
        throw new \ECSPrefix20210517\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $position
     */
    private function fixStartToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $position)
    {
        $position = (int) $position;
        $match = \ECSPrefix20210517\Nette\Utils\Strings::match($token->getContent(), self::START_HEREDOC_NOWDOC_NAME_REGEX);
        if (!isset($match['name'])) {
            return;
        }
        $newContent = \ECSPrefix20210517\Nette\Utils\Strings::replace($token->getContent(), self::START_HEREDOC_NOWDOC_NAME_REGEX, '$1' . $this->keyword . '$4');
        $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $newContent]);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $position
     */
    private function fixEndToken(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $position)
    {
        $position = (int) $position;
        if ($token->getContent() === $this->keyword) {
            return;
        }
        $tokenContent = $token->getContent();
        $trimmedTokenContent = \trim($tokenContent);
        $spaceEnd = '';
        if (\PHP_VERSION_ID >= 70300 && $tokenContent !== $trimmedTokenContent) {
            $spaceEnd = \substr($tokenContent, 0, \strlen($tokenContent) - \strlen($trimmedTokenContent));
        }
        $tokens[$position] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $spaceEnd . $this->keyword]);
    }
}
