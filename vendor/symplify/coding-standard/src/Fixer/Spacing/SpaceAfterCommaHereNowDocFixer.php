<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Spacing;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix202408\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Spacing\SpaceAfterCommaHereNowDocFixer\SpaceAfterCommaHereNowDocFixerTest
 * @see https://3v4l.org/KPZXU
 */
final class SpaceAfterCommaHereNowDocFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Add space after nowdoc and heredoc keyword, to prevent bugs on PHP 7.2 and lower, see https://laravel-news.com/flexible-heredoc-and-nowdoc-coming-to-php-7-3';
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_START_HEREDOC, \T_START_NOWDOC]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens) : void
    {
        // function arguments, function call parameters, lambda use()
        for ($position = \count($tokens) - 1; $position >= 0; --$position) {
            /** @var Token $token */
            $token = $tokens[$position];
            if (!$token->isGivenKind(\T_END_HEREDOC)) {
                continue;
            }
            // nothing
            if (!isset($tokens[$position + 1])) {
                continue;
            }
            /** @var Token $nextToken */
            $nextToken = $tokens[$position + 1];
            if (!\in_array($nextToken->getContent(), [',', ']'], \true)) {
                continue;
            }
            $tokens->ensureWhitespaceAtIndex($position + 1, 0, \PHP_EOL);
        }
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
$values = [
    <<<RECTIFY
Some content
RECTIFY,
    1000
];
CODE_SAMPLE
, <<<'CODE_SAMPLE'
$values = [
    <<<RECTIFY
Some content
RECTIFY
,
    1000
];
CODE_SAMPLE
)]);
    }
}
