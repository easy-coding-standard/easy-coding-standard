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
namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Gregor Harlan <gharlan@web.de>
 */
final class NoUnneededControlParenthesesFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @var array
     */
    private static $loops = ['break' => ['lookupTokens' => \T_BREAK, 'neededSuccessors' => [';']], 'clone' => ['lookupTokens' => \T_CLONE, 'neededSuccessors' => [';', ':', ',', ')'], 'forbiddenContents' => ['?', ':', [\T_COALESCE, '??']]], 'continue' => ['lookupTokens' => \T_CONTINUE, 'neededSuccessors' => [';']], 'echo_print' => ['lookupTokens' => [\T_ECHO, \T_PRINT], 'neededSuccessors' => [';', [\T_CLOSE_TAG]]], 'return' => ['lookupTokens' => \T_RETURN, 'neededSuccessors' => [';', [\T_CLOSE_TAG]]], 'switch_case' => ['lookupTokens' => \T_CASE, 'neededSuccessors' => [';', ':']], 'yield' => ['lookupTokens' => \T_YIELD, 'neededSuccessors' => [';', ')']], 'yield_from' => ['lookupTokens' => \T_YIELD_FROM, 'neededSuccessors' => [';', ')']]];
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        $types = [];
        foreach (self::$loops as $loop) {
            $types[] = (array) $loop['lookupTokens'];
        }
        $types = \array_merge(...$types);
        return $tokens->isAnyTokenKindsFound($types);
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Removes unneeded parentheses around control statements.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
while ($x) { while ($y) { break (2); } }
clone($a);
while ($y) { continue (2); }
echo("foo");
print("foo");
return (1 + 2);
switch ($a) { case($x); }
yield(2);
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
while ($x) { while ($y) { break (2); } }
clone($a);
while ($y) { continue (2); }
echo("foo");
print("foo");
return (1 + 2);
switch ($a) { case($x); }
yield(2);
', ['statements' => ['break', 'continue']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoTrailingWhitespaceFixer.
     */
    public function getPriority() : int
    {
        return 30;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // Checks if specific statements are set and uses them in this case.
        $loops = \array_intersect_key(self::$loops, \array_flip($this->configuration['statements']));
        foreach ($tokens as $index => $token) {
            if (!$token->equalsAny(['(', [\PhpCsFixer\Tokenizer\CT::T_BRACE_CLASS_INSTANTIATION_OPEN]])) {
                continue;
            }
            $blockStartIndex = $index;
            $index = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$index];
            foreach ($loops as $loop) {
                if (!$prevToken->isGivenKind($loop['lookupTokens'])) {
                    continue;
                }
                $blockEndIndex = $tokens->findBlockEnd($token->equals('(') ? \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_BRACE_CLASS_INSTANTIATION, $blockStartIndex);
                $blockEndNextIndex = $tokens->getNextMeaningfulToken($blockEndIndex);
                if (!$tokens[$blockEndNextIndex]->equalsAny($loop['neededSuccessors'])) {
                    continue;
                }
                if (\array_key_exists('forbiddenContents', $loop)) {
                    $forbiddenTokenIndex = $tokens->getNextTokenOfKind($blockStartIndex, $loop['forbiddenContents']);
                    // A forbidden token is found and is inside the parenthesis.
                    if (null !== $forbiddenTokenIndex && $forbiddenTokenIndex < $blockEndIndex) {
                        continue;
                    }
                }
                if ($tokens[$blockStartIndex - 1]->isWhitespace() || $tokens[$blockStartIndex - 1]->isComment()) {
                    $tokens->clearTokenAndMergeSurroundingWhitespace($blockStartIndex);
                } else {
                    // Adds a space to prevent broken code like `return2`.
                    $tokens[$blockStartIndex] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                }
                $tokens->clearTokenAndMergeSurroundingWhitespace($blockEndIndex);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('statements', 'List of control statements to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$loops))])->setDefault(['break', 'clone', 'continue', 'echo_print', 'return', 'switch_case', 'yield'])->getOption()]);
    }
}
