<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use ECSPrefix20210606\Nette\Utils\Strings;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;
use Symplify\CodingStandard\DocBlock\Decommenter;
use Symplify\CodingStandard\Fixer\AbstractSymplifyFixer;
use Symplify\CodingStandard\Php\PhpContentAnalyzer;
use Symplify\CodingStandard\Tokens\CommentedContentResolver;
use Symplify\CodingStandard\ValueObject\StartAndEnd;
use ECSPrefix20210606\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @see https://softwareengineering.stackexchange.com/a/394288/148956
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveCommentedCodeFixer\RemoveCommentedCodeFixerTest
 */
final class RemoveCommentedCodeFixer extends \Symplify\CodingStandard\Fixer\AbstractSymplifyFixer implements \ECSPrefix20210606\Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Remove commented code like "// $one = 1000;"';
    /**
     * @var CommentedContentResolver
     */
    private $commentedContentResolver;
    /**
     * @var PhpContentAnalyzer
     */
    private $phpContentAnalyzer;
    /**
     * @var Decommenter
     */
    private $decommenter;
    public function __construct(\Symplify\CodingStandard\Tokens\CommentedContentResolver $commentedContentResolver, \Symplify\CodingStandard\Php\PhpContentAnalyzer $phpContentAnalyzer, \Symplify\CodingStandard\DocBlock\Decommenter $decommenter)
    {
        $this->commentedContentResolver = $commentedContentResolver;
        $this->phpContentAnalyzer = $phpContentAnalyzer;
        $this->decommenter = $decommenter;
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
        return $tokens->isTokenKindFound(\T_COMMENT);
    }
    /**
     * @param Tokens<Token> $tokens
     * @return void
     */
    public function fix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $contentWithPositions = [];
        for ($i = 0; $i < $tokens->count(); ++$i) {
            $token = $tokens[$i];
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            if (!\ECSPrefix20210606\Nette\Utils\Strings::startsWith($token->getContent(), '//')) {
                continue;
            }
            $startAndEnd = $this->commentedContentResolver->resolve($tokens, $i);
            // new position to jump to
            $i = $startAndEnd->getEnd() + 1;
            $commentedContent = $tokens->generatePartialCode($startAndEnd->getStart(), $startAndEnd->getEnd());
            $possiblePhpContent = $this->decommenter->decoment($commentedContent);
            if (!$this->phpContentAnalyzer->isPhpContent($possiblePhpContent)) {
                continue;
            }
            // clear PHP commented content
            $contentWithPositions[] = $startAndEnd;
        }
        /** @var StartAndEnd[] $contentWithPositions */
        $contentWithPositions = \array_reverse($contentWithPositions);
        foreach ($contentWithPositions as $startAndEnd) {
            $realStart = $this->resolveRealStart($startAndEnd, $tokens);
            $tokens->clearRange($realStart, $startAndEnd->getEnd());
        }
    }
    public function getRuleDefinition() : \ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\RuleDefinition
    {
        return new \ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\RuleDefinition(self::ERROR_MESSAGE, [new \ECSPrefix20210606\Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample(<<<'CODE_SAMPLE'
// $one = 1;
// $two = 2;
// $three = 3;
CODE_SAMPLE
, <<<'CODE_SAMPLE'
CODE_SAMPLE
)]);
    }
    /**
     * Remove the indent space ahead of comments
     *
     * @param Tokens<Token> $tokens
     */
    private function resolveRealStart(\Symplify\CodingStandard\ValueObject\StartAndEnd $startAndEnd, \PhpCsFixer\Tokenizer\Tokens $tokens) : int
    {
        $preStartPosition = $startAndEnd->getStart() - 1;
        /** @var Token $preStartToken */
        $preStartToken = $tokens[$preStartPosition];
        $realStart = $startAndEnd->getStart();
        if ($preStartToken->getContent() === \PHP_EOL) {
            return $realStart - 1;
        }
        if (\ECSPrefix20210606\Nette\Utils\Strings::endsWith($preStartToken->getContent(), '    ')) {
            return $realStart - 1;
        }
        return $realStart;
    }
}
