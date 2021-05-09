<?php

namespace Symplify\CodingStandard\Fixer\Annotation;

use Doctrine\Common\Annotations\DocLexer;
use Nette\Utils\Strings;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use Symplify\CodingStandard\TokenRunner\Analyzer\FixerAnalyzer\DoctrineBlockFinder;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\RuleDocGenerator\Contract\DocumentedRuleInterface;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Annotation\DoctrineAnnotationNewlineInNestedAnnotationFixer\DoctrineAnnotationNewlineInNestedAnnotationFixerTest
 */
final class DoctrineAnnotationNewlineInNestedAnnotationFixer extends AbstractDoctrineAnnotationFixer implements DocumentedRuleInterface
{
    /**
     * @var string
     */
    const ERROR_MESSAGE = 'Nested object annotations should start on a standalone line';

    /**
     * @var DoctrineBlockFinder
     */
    private $doctrineBlockFinder;

    /**
     * @var BlockInfo|null
     */
    private $currentBlockInfo;

    public function __construct(DoctrineBlockFinder $doctrineBlockFinder)
    {
        $this->doctrineBlockFinder = $doctrineBlockFinder;

        parent::__construct();
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        // must run before \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
        return 100;
    }

    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }

    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition()
    {
        return new RuleDefinition(self::ERROR_MESSAGE, [
            new CodeSample(
                <<<'CODE_SAMPLE'
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={@ORM\Index(name="user_id", columns={"another_id"})})
 */
class SomeEntity
{
}
CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user", indexes={
 * @ORM\Index(name="user_id", columns={"another_id"})
 * })
 */
class SomeEntity
{
}
CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @note indent is covered by
     * @see \PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer
     *
     * @param iterable<Token>&Tokens $tokens
     * @return void
     */
    protected function fixAnnotations(Tokens $tokens)
    {
        $this->currentBlockInfo = null;

        $tokenCount = $tokens->count();

        // what about foreach?
        for ($index = 0; $index < $tokenCount; ++$index) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$index];
            if (! $currentToken->isType(DocLexer::T_AT)) {
                continue;
            }

            $previousTokenPosition = $index - 1;
            $previousToken = isset($tokens[$previousTokenPosition]) ? $tokens[$previousTokenPosition] : null;
            if ($previousToken === null) {
                continue;
            }

            if ($this->shouldSkip($index, $tokens, $previousToken)) {
                continue;
            }

            $tokens->insertAt($index, new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($index, new Token(DocLexer::T_NONE, "\n"));
            $tNone = $previousToken->isType(DocLexer::T_NONE);

            // remove redundant space
            if ($tNone) {
                $tokens->offsetUnset($previousTokenPosition);
            }

            $this->processEndBracket($index, $tokens, $previousTokenPosition);
        }
    }

    /**
     * @return bool
     */
    private function isDocOpener(Token $token)
    {
        if ($token->isType(DocLexer::T_NONE)) {
            return Strings::contains($token->getContent(), '*');
        }

        return false;
    }

    /**
     * @param Tokens<Token> $tokens
     * @return void
     * @param int $index
     * @param int $previousTokenPosition
     */
    private function processEndBracket($index, Tokens $tokens, $previousTokenPosition)
    {
        $index = (int) $index;
        $previousTokenPosition = (int) $previousTokenPosition;
        /** @var Token $previousToken */
        $previousToken = $tokens->offsetGet($previousTokenPosition);
        // already a space → skip
        if ($previousToken->isType(DocLexer::T_NONE)) {
            return;
        }

        // reset
        if ($this->currentBlockInfo !== null && ! $this->currentBlockInfo->contains($index)) {
            $this->currentBlockInfo = null;
            return;
        }

        if ($this->currentBlockInfo === null) {
            $this->currentBlockInfo = $this->doctrineBlockFinder->findInTokensByEdge(
                $tokens,
                $previousTokenPosition
            );
        }

        if ($this->currentBlockInfo !== null) {
            $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, ' * '));
            $tokens->insertAt($this->currentBlockInfo->getEnd(), new Token(DocLexer::T_NONE, "\n"));
        }
    }

    /**
     * @param Tokens<Token> $tokens
     * @param int $index
     * @return bool
     */
    private function shouldSkip($index, Tokens $tokens, Token $previousToken)
    {
        $index = (int) $index;
        // docblock opener → skip it
        if ($this->isDocOpener($previousToken)) {
            return true;
        }

        $nextTokenPosition = $index + 1;

        $nextToken = isset($tokens[$nextTokenPosition]) ? $tokens[$nextTokenPosition] : null;
        if (! $nextToken instanceof Token) {
            return true;
        }

        if (! Strings::startsWith($nextToken->getContent(), 'ORM')) {
            return true;
        }

        // not an entity annotation, just some comment
        return $nextToken->getContent() === 'ORM';
    }
}
