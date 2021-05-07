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
namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NewWithBracesFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('All instances created with new keyword must be followed by braces.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \$x = new X;\n")]);
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isTokenKindFound(\T_NEW);
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        static $nextTokenKinds = null;
        if (null === $nextTokenKinds) {
            $nextTokenKinds = ['?', ';', ',', '(', ')', '[', ']', ':', '<', '>', '+', '-', '*', '/', '%', '&', '^', '|', [\T_CLASS], [\T_IS_SMALLER_OR_EQUAL], [\T_IS_GREATER_OR_EQUAL], [\T_IS_EQUAL], [\T_IS_NOT_EQUAL], [\T_IS_IDENTICAL], [\T_IS_NOT_IDENTICAL], [\T_CLOSE_TAG], [\T_LOGICAL_AND], [\T_LOGICAL_OR], [\T_LOGICAL_XOR], [\T_BOOLEAN_AND], [\T_BOOLEAN_OR], [\T_SL], [\T_SR], [\T_INSTANCEOF], [\T_AS], [\T_DOUBLE_ARROW], [\T_POW], [\T_SPACESHIP], [\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN], [\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE], [\PhpCsFixer\Tokenizer\CT::T_BRACE_CLASS_INSTANTIATION_OPEN], [\PhpCsFixer\Tokenizer\CT::T_BRACE_CLASS_INSTANTIATION_CLOSE]];
        }
        for ($index = $tokens->count() - 3; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_NEW)) {
                continue;
            }
            $nextIndex = $tokens->getNextTokenOfKind($index, $nextTokenKinds);
            $nextToken = $tokens[$nextIndex];
            // new anonymous class definition
            if ($nextToken->isGivenKind(\T_CLASS)) {
                if (!$tokens[$tokens->getNextMeaningfulToken($nextIndex)]->equals('(')) {
                    $this->insertBracesAfter($tokens, $nextIndex);
                }
                continue;
            }
            // entrance into array index syntax - need to look for exit
            while ($nextToken->equals('[') || $nextToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)) {
                $nextIndex = $tokens->findBlockEnd($tokens->detectBlockType($nextToken)['type'], $nextIndex) + 1;
                $nextToken = $tokens[$nextIndex];
            }
            // new statement has a gap in it - advance to the next token
            if ($nextToken->isWhitespace()) {
                $nextIndex = $tokens->getNextNonWhitespace($nextIndex);
                $nextToken = $tokens[$nextIndex];
            }
            // new statement with () - nothing to do
            if ($nextToken->equals('(') || $nextToken->isObjectOperator()) {
                continue;
            }
            $this->insertBracesAfter($tokens, $tokens->getPrevMeaningfulToken($nextIndex));
        }
    }
    /**
     * @return void
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     */
    private function insertBracesAfter($tokens, $index)
    {
        $tokens->insertAt(++$index, [new \PhpCsFixer\Tokenizer\Token('('), new \PhpCsFixer\Tokenizer\Token(')')]);
    }
}
