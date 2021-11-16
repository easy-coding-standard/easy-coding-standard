<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use PhpCsFixer\Utils;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoExtraBlankLinesFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var string[]
     */
    private static $availableTokens = ['break', 'case', 'continue', 'curly_brace_block', 'default', 'extra', 'parenthesis_brace_block', 'return', 'square_brace_block', 'switch', 'throw', 'use', 'use_trait'];
    /**
     * @var array<int, string> key is token id, value is name of callback
     */
    private $tokenKindCallbackMap;
    /**
     * @var array<string, string> token prototype, value is name of callback
     */
    private $tokenEqualsMap;
    /**
     * @var Tokens
     */
    private $tokens;
    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration) : void
    {
        if (isset($configuration['tokens']) && \in_array('use_trait', $configuration['tokens'], \true)) {
            \PhpCsFixer\Utils::triggerDeprecation(new \RuntimeException('Option "tokens: use_trait" used in `no_extra_blank_lines` rule is deprecated, use the rule `class_attributes_separation` with `elements: trait_import` instead.'));
        }
        parent::configure($configuration);
        static $reprToTokenMap = ['break' => \T_BREAK, 'case' => \T_CASE, 'continue' => \T_CONTINUE, 'curly_brace_block' => '{', 'default' => \T_DEFAULT, 'extra' => \T_WHITESPACE, 'parenthesis_brace_block' => '(', 'return' => \T_RETURN, 'square_brace_block' => \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, 'switch' => \T_SWITCH, 'throw' => \T_THROW, 'use' => \T_USE, 'use_trait' => \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT];
        static $tokenKindCallbackMap = [\T_BREAK => 'fixAfterToken', \T_CASE => 'fixAfterToken', \T_CONTINUE => 'fixAfterToken', \T_DEFAULT => 'fixAfterToken', \T_RETURN => 'fixAfterToken', \T_SWITCH => 'fixAfterToken', \T_THROW => 'fixAfterThrowToken', \T_USE => 'removeBetweenUse', \T_WHITESPACE => 'removeMultipleBlankLines', \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT => 'removeBetweenUse', \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN => 'fixStructureOpenCloseIfMultiLine'];
        static $tokenEqualsMap = [
            '{' => 'fixStructureOpenCloseIfMultiLine',
            // i.e. not: CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN
            '(' => 'fixStructureOpenCloseIfMultiLine',
        ];
        $tokensAssoc = \array_flip(\array_intersect_key($reprToTokenMap, \array_flip($this->configuration['tokens'])));
        $this->tokenKindCallbackMap = \array_intersect_key($tokenKindCallbackMap, $tokensAssoc);
        $this->tokenEqualsMap = \array_intersect_key($tokenEqualsMap, $tokensAssoc);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Removes extra blank lines and/or blank lines following configuration.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array("foo");


$bar = "bar";
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

switch ($foo) {
    case 41:
        echo "foo";
        break;

    case 42:
        break;
}
', ['tokens' => ['break']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

for ($i = 0; $i < 9000; ++$i) {
    if (true) {
        continue;

    }
}
', ['tokens' => ['continue']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

for ($i = 0; $i < 9000; ++$i) {

    echo $i;

}
', ['tokens' => ['curly_brace_block']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array("foo");


$bar = "bar";
', ['tokens' => ['extra']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = array(

    "foo"

);
', ['tokens' => ['parenthesis_brace_block']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

function foo($bar)
{
    return $bar;

}
', ['tokens' => ['return']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

$foo = [

    "foo"

];
', ['tokens' => ['square_brace_block']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

function foo($bar)
{
    throw new \\Exception("Hello!");

}
', ['tokens' => ['throw']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php

namespace Foo;

use Bar\\Baz;

use Baz\\Bar;

class Bar
{
}
', ['tokens' => ['use']]), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
switch($a) {

    case 1:

    default:

        echo 3;
}
', ['tokens' => ['switch', 'case', 'default']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BlankLineBeforeStatementFixer.
     * Must run after ClassAttributesSeparationFixer, CombineConsecutiveUnsetsFixer, EmptyLoopBodyFixer, EmptyLoopConditionFixer, FunctionToConstantFixer, ModernizeStrposFixer, NoEmptyCommentFixer, NoEmptyPhpdocFixer, NoEmptyStatementFixer, NoUnusedImportsFixer, NoUselessElseFixer, NoUselessReturnFixer, NoUselessSprintfFixer, StringLengthToEmptyFixer.
     */
    public function getPriority() : int
    {
        return -20;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $this->tokens = $tokens;
        $this->tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($this->tokens);
        for ($index = $tokens->getSize() - 1; $index > 0; --$index) {
            $this->fixByToken($tokens[$index], $index);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('tokens', 'List of tokens to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset(self::$availableTokens)])->setDefault(['extra'])->getOption()]);
    }
    private function fixByToken(\PhpCsFixer\Tokenizer\Token $token, int $index) : void
    {
        foreach ($this->tokenKindCallbackMap as $kind => $callback) {
            if (!$token->isGivenKind($kind)) {
                continue;
            }
            $this->{$callback}($index);
            return;
        }
        foreach ($this->tokenEqualsMap as $equals => $callback) {
            if (!$token->equals($equals)) {
                continue;
            }
            $this->{$callback}($index);
            return;
        }
    }
    private function removeBetweenUse(int $index) : void
    {
        $next = $this->tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
        if (null === $next || $this->tokens[$next]->isGivenKind(\T_CLOSE_TAG)) {
            return;
        }
        $nextUseCandidate = $this->tokens->getNextMeaningfulToken($next);
        if (null === $nextUseCandidate || !$this->tokens[$nextUseCandidate]->isGivenKind($this->tokens[$index]->getId()) || !$this->containsLinebreak($index, $nextUseCandidate)) {
            return;
        }
        $this->removeEmptyLinesAfterLineWithTokenAt($next);
    }
    private function removeMultipleBlankLines(int $index) : void
    {
        $expected = $this->tokens[$index - 1]->isGivenKind(\T_OPEN_TAG) && 1 === \PhpCsFixer\Preg::match('/\\R$/', $this->tokens[$index - 1]->getContent()) ? 1 : 2;
        $parts = \PhpCsFixer\Preg::split('/(.*\\R)/', $this->tokens[$index]->getContent(), -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY);
        $count = \count($parts);
        if ($count > $expected) {
            $this->tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \implode('', \array_slice($parts, 0, $expected)) . \rtrim($parts[$count - 1], "\r\n")]);
        }
    }
    private function fixAfterToken(int $index) : void
    {
        for ($i = $index - 1; $i > 0; --$i) {
            if ($this->tokens[$i]->isGivenKind(\T_FUNCTION) && $this->tokensAnalyzer->isLambda($i)) {
                return;
            }
            if ($this->tokens[$i]->isGivenKind(\T_CLASS) && $this->tokensAnalyzer->isAnonymousClass($i)) {
                return;
            }
            if ($this->tokens[$i]->isWhitespace() && \strpos($this->tokens[$i]->getContent(), "\n") !== \false) {
                break;
            }
        }
        $this->removeEmptyLinesAfterLineWithTokenAt($index);
    }
    private function fixAfterThrowToken(int $index) : void
    {
        if ($this->tokens[$this->tokens->getPrevMeaningfulToken($index)]->equalsAny([';', '{', '}', ':', [\T_OPEN_TAG]])) {
            $this->fixAfterToken($index);
        }
    }
    /**
     * Remove white line(s) after the index of a block type,
     * but only if the block is not on one line.
     *
     * @param int $index body start
     */
    private function fixStructureOpenCloseIfMultiLine(int $index) : void
    {
        $blockTypeInfo = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($this->tokens[$index]);
        $bodyEnd = $this->tokens->findBlockEnd($blockTypeInfo['type'], $index);
        for ($i = $bodyEnd - 1; $i >= $index; --$i) {
            if (\strpos($this->tokens[$i]->getContent(), "\n") !== \false) {
                $this->removeEmptyLinesAfterLineWithTokenAt($i);
                $this->removeEmptyLinesAfterLineWithTokenAt($index);
                break;
            }
        }
    }
    private function removeEmptyLinesAfterLineWithTokenAt(int $index) : void
    {
        // find the line break
        $tokenCount = \count($this->tokens);
        for ($end = $index; $end < $tokenCount; ++$end) {
            if ($this->tokens[$end]->equals('}') || \strpos($this->tokens[$end]->getContent(), "\n") !== \false) {
                break;
            }
        }
        if ($end === $tokenCount) {
            return;
            // not found, early return
        }
        $ending = $this->whitespacesConfig->getLineEnding();
        for ($i = $end; $i < $tokenCount && $this->tokens[$i]->isWhitespace(); ++$i) {
            $content = $this->tokens[$i]->getContent();
            if (\substr_count($content, "\n") < 1) {
                continue;
            }
            $pos = \strrpos($content, "\n");
            if ($pos + 2 <= \strlen($content)) {
                // preserve indenting where possible
                $newContent = $ending . \substr($content, $pos + 1);
            } else {
                $newContent = $ending;
            }
            $this->tokens[$i] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newContent]);
        }
    }
    private function containsLinebreak(int $startIndex, int $endIndex) : bool
    {
        for ($i = $endIndex; $i > $startIndex; --$i) {
            if (\PhpCsFixer\Preg::match('/\\R/', $this->tokens[$i]->getContent())) {
                return \true;
            }
        }
        return \false;
    }
}
