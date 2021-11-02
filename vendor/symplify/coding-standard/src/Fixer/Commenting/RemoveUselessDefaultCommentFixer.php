<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\DocBlock\UselessDocBlockCleaner;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use ECSPrefix20211102\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20211102\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20211102\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveUselessDefaultCommentFixer\RemoveUselessDefaultCommentFixerTest
 */
final class RemoveUselessDefaultCommentFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20211102\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove useless PHPStorm-generated @todo comments, redundant "Class XY" or "gets service" comments etc.';
    /**
     * @var \Symplify\CodingStandard\DocBlock\UselessDocBlockCleaner
     */
    private $uselessDocBlockCleaner;
    public function __construct(\Symplify\CodingStandard\DocBlock\UselessDocBlockCleaner $uselessDocBlockCleaner)
    {
        $this->uselessDocBlockCleaner = $uselessDocBlockCleaner;
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
        return $tokens->isAnyTokenKindsFound([\T_DOC_COMMENT, \T_COMMENT]);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    public function fix(\SplFileInfo $fileInfo, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $reversedTokens = $this->reverseTokens($tokens);
        foreach ($reversedTokens as $index => $token) {
            if (!$token->isGivenKind([\T_DOC_COMMENT, \T_COMMENT])) {
                continue;
            }
            $cleanedDocContent = $this->uselessDocBlockCleaner->clearDocTokenContent($reversedTokens, $index, $token->getContent());
            if ($cleanedDocContent !== '') {
                continue;
            }
            // remove token
            $tokens->clearAt($index);
        }
    }
    public function getRuleDefinition() : \ECSPrefix20211102\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20211102\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20211102\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
/**
 * class SomeClass
 */
class SomeClass
{
    /**
     * SomeClass Constructor.
     */
    public function __construct()
    {
        // TODO: Change the autogenerated stub
        // TODO: Implement whatever() method.
    }
}
CODE_SAMPLE
, <<<'CODE_SAMPLE'
class SomeClass
{
    public function __construct()
    {
    }
}
CODE_SAMPLE
)]);
    }
}
