<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Annotation;

use ECSPrefix202208\Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix202208\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix202208\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix202208\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\RemovePHPStormAnnotationFixer\RemovePHPStormAnnotationFixerTest
 */
final class RemovePHPStormAnnotationFixer extends AbstractSymplifyFixer implements DocumentedRuleInterface
{
    /**
     * @see https://regex101.com/r/nGZBzj/2
     * @var string
     */
    private const CREATED_BY_PHPSTORM_DOC_REGEX = '#\\/\\*\\*\\s+\\*\\s+Created by PHPStorm(.*?)\\*\\/#msi';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove "Created by PhpStorm" annotations';
    public function getDefinition() : FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_DOC_COMMENT, \T_COMMENT]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(SplFileInfo $fileInfo, Tokens $tokens) : void
    {
        $reversedTokens = $this->reverseTokens($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind([\T_DOC_COMMENT, \T_COMMENT])) {
                continue;
            }
            $originalDocContent = $token->getContent();
            $cleanedDocContent = Strings::replace($originalDocContent, self::CREATED_BY_PHPSTORM_DOC_REGEX, '');
            if ($cleanedDocContent !== '') {
                continue;
            }
            // remove token
            $tokens->clearAt($index);
        }
    }
    public function getRuleDefinition() : RuleDefinition
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [new CodeSample(<<<'CODE_SAMPLE'
/**
 * Created by PhpStorm.
 * User: ...
 * Date: 17/10/17
 * Time: 8:50 AM
 */
class SomeClass
{
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
}
CODE_SAMPLE
)]);
    }
}
