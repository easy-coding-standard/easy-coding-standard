<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class CommentToPhpdocFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var string[]
     */
    private $ignoredTags = [];
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     *
     * Must run before GeneralPhpdocAnnotationRemoveFixer, GeneralPhpdocTagRenameFixer, NoBlankLinesAfterPhpdocFixer, NoEmptyPhpdocFixer, NoSuperfluousPhpdocTagsFixer, PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer, PhpdocAlignFixer, PhpdocAnnotationWithoutDotFixer, PhpdocInlineTagNormalizerFixer, PhpdocLineSpanFixer, PhpdocNoAccessFixer, PhpdocNoAliasTagFixer, PhpdocNoEmptyReturnFixer, PhpdocNoPackageFixer, PhpdocNoUselessInheritdocFixer, PhpdocOrderByValueFixer, PhpdocOrderFixer, PhpdocReturnSelfReferenceFixer, PhpdocSeparationFixer, PhpdocSingleLineVarSpacingFixer, PhpdocSummaryFixer, PhpdocTagCasingFixer, PhpdocTagTypeFixer, PhpdocToCommentFixer, PhpdocToParamTypeFixer, PhpdocToPropertyTypeFixer, PhpdocToReturnTypeFixer, PhpdocTrimConsecutiveBlankLineSeparationFixer, PhpdocTrimFixer, PhpdocTypesOrderFixer, PhpdocVarAnnotationCorrectOrderFixer, PhpdocVarWithoutNameFixer.
     * Must run after AlignMultilineCommentFixer.
     * @return int
     */
    public function getPriority()
    {
        // Should be run before all other PHPDoc fixers
        return 26;
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Comments with annotation should be docblock when used on structural elements.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php /* header */ \$x = true; /* @var bool \$isFoo */ \$isFoo = true;\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n// @todo do something later\n\$foo = 1;\n\n// @var int \$a\n\$a = foo();\n", ['ignored_tags' => ['todo']])], null, 'Risky as new docblocks might mean more, e.g. a Doctrine entity might have a new column in database.');
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);
        $this->ignoredTags = \array_map(static function (string $tag) {
            return \strtolower($tag);
        }, $this->configuration['ignored_tags']);
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('ignored_tags', 'List of ignored tags'))->setAllowedTypes(['array'])->setDefault([])->getOption()]);
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $commentsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\CommentsAnalyzer();
        for ($index = 0, $limit = \count($tokens); $index < $limit; ++$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            if ($commentsAnalyzer->isHeaderComment($tokens, $index)) {
                continue;
            }
            if (!$commentsAnalyzer->isBeforeStructuralElement($tokens, $index)) {
                continue;
            }
            $commentIndices = $commentsAnalyzer->getCommentBlockIndices($tokens, $index);
            if ($this->isCommentCandidate($tokens, $commentIndices)) {
                $this->fixComment($tokens, $commentIndices);
            }
            $index = \max($commentIndices);
        }
    }
    /**
     * @param int[] $indices
     * @return bool
     */
    private function isCommentCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices)
    {
        return \array_reduce($indices, function (bool $carry, int $index) use($tokens) {
            if ($carry) {
                return \true;
            }
            if (1 !== \PhpCsFixer\Preg::match('~(?:#|//|/\\*+|\\R(?:\\s*\\*)?)\\s*\\@([a-zA-Z0-9_\\\\-]+)(?=\\s|\\(|$)~', $tokens[$index]->getContent(), $matches)) {
                return \false;
            }
            return !\in_array(\strtolower($matches[1]), $this->ignoredTags, \true);
        }, \false);
    }
    /**
     * @param int[] $indices
     * @return void
     */
    private function fixComment(\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices)
    {
        if (1 === \count($indices)) {
            $this->fixCommentSingleLine($tokens, \reset($indices));
        } else {
            $this->fixCommentMultiLine($tokens, $indices);
        }
    }
    /**
     * @return void
     * @param int $index
     */
    private function fixCommentSingleLine(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        $message = $this->getMessage($tokens[$index]->getContent());
        if ('' !== \trim(\substr($message, 0, 1))) {
            $message = ' ' . $message;
        }
        if ('' !== \trim(\substr($message, -1))) {
            $message .= ' ';
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, '/**' . $message . '*/']);
    }
    /**
     * @param int[] $indices
     * @return void
     */
    private function fixCommentMultiLine(\PhpCsFixer\Tokenizer\Tokens $tokens, array $indices)
    {
        $startIndex = \reset($indices);
        $indent = \PhpCsFixer\Utils::calculateTrailingWhitespaceIndent($tokens[$startIndex - 1]);
        $newContent = '/**' . $this->whitespacesConfig->getLineEnding();
        $count = \max($indices);
        for ($index = $startIndex; $index <= $count; ++$index) {
            if (!$tokens[$index]->isComment()) {
                continue;
            }
            if (\false !== \strpos($tokens[$index]->getContent(), '*/')) {
                return;
            }
            $newContent .= $indent . ' *' . $this->getMessage($tokens[$index]->getContent()) . $this->whitespacesConfig->getLineEnding();
        }
        for ($index = $startIndex; $index <= $count; ++$index) {
            $tokens->clearAt($index);
        }
        $newContent .= $indent . ' */';
        $tokens->insertAt($startIndex, new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $newContent]));
    }
    /**
     * @param string $content
     * @return string
     */
    private function getMessage($content)
    {
        $content = (string) $content;
        if (0 === \strpos($content, '#')) {
            return \substr($content, 1);
        }
        if (0 === \strpos($content, '//')) {
            return \substr($content, 2);
        }
        return \rtrim(\ltrim($content, '/*'), '*/');
    }
}
