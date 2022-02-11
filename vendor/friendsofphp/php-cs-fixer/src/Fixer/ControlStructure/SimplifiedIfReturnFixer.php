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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class SimplifiedIfReturnFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * @var array[]
     */
    private $sequences = [['isNegative' => \false, 'sequence' => ['{', [\T_RETURN], [\T_STRING, 'true'], ';', '}', [\T_RETURN], [\T_STRING, 'false'], ';']], ['isNegative' => \true, 'sequence' => ['{', [\T_RETURN], [\T_STRING, 'false'], ';', '}', [\T_RETURN], [\T_STRING, 'true'], ';']], ['isNegative' => \false, 'sequence' => [[\T_RETURN], [\T_STRING, 'true'], ';', [\T_RETURN], [\T_STRING, 'false'], ';']], ['isNegative' => \true, 'sequence' => [[\T_RETURN], [\T_STRING, 'false'], ';', [\T_RETURN], [\T_STRING, 'true'], ';']]];
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Simplify `if` control structures that return the boolean result of their condition.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nif (\$foo) { return true; } return false;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer.
     * Must run after NoSuperfluousElseifFixer, NoUnneededCurlyBracesFixer, NoUselessElseFixer, SemicolonAfterInstructionFixer.
     */
    public function getPriority() : int
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_IF, \T_RETURN, \T_STRING]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($ifIndex = $tokens->count() - 1; 0 <= $ifIndex; --$ifIndex) {
            if (!$tokens[$ifIndex]->isGivenKind([\T_IF, \T_ELSEIF])) {
                continue;
            }
            if ($tokens[$tokens->getPrevMeaningfulToken($ifIndex)]->equals(')')) {
                continue;
                // in a loop without braces
            }
            $startParenthesisIndex = $tokens->getNextTokenOfKind($ifIndex, ['(']);
            $endParenthesisIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $firstCandidateIndex = $tokens->getNextMeaningfulToken($endParenthesisIndex);
            foreach ($this->sequences as $sequenceSpec) {
                $sequenceFound = $tokens->findSequence($sequenceSpec['sequence'], $firstCandidateIndex);
                if (null === $sequenceFound) {
                    continue;
                }
                $firstSequenceIndex = \key($sequenceFound);
                if ($firstSequenceIndex !== $firstCandidateIndex) {
                    continue;
                }
                $indicesToClear = \array_keys($sequenceFound);
                \array_pop($indicesToClear);
                // Preserve last semicolon
                \rsort($indicesToClear);
                foreach ($indicesToClear as $index) {
                    $tokens->clearTokenAndMergeSurroundingWhitespace($index);
                }
                $newTokens = [new \PhpCsFixer\Tokenizer\Token([\T_RETURN, 'return']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])];
                if ($sequenceSpec['isNegative']) {
                    $newTokens[] = new \PhpCsFixer\Tokenizer\Token('!');
                } else {
                    $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_BOOL_CAST, '(bool)']);
                }
                $tokens->overrideRange($ifIndex, $ifIndex, $newTokens);
            }
        }
    }
}
