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
namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractNoUselessElseFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class NoSuperfluousElseifFixer extends \PhpCsFixer\AbstractNoUselessElseFixer
{
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_ELSE, \T_ELSEIF]);
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Replaces superfluous `elseif` with `if`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nif (\$a) {\n    return 1;\n} elseif (\$b) {\n    return 2;\n}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before SimplifiedIfReturnFixer.
     * Must run after NoAlternativeSyntaxFixer.
     * @return int
     */
    public function getPriority()
    {
        return parent::getPriority();
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if ($this->isElseif($tokens, $index) && $this->isSuperfluousElse($tokens, $index)) {
                $this->convertElseifToIf($tokens, $index);
            }
        }
    }
    /**
     * @param int $index
     * @return bool
     */
    private function isElseif(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        return $tokens[$index]->isGivenKind(\T_ELSEIF) || $tokens[$index]->isGivenKind(\T_ELSE) && $tokens[$tokens->getNextMeaningfulToken($index)]->isGivenKind(\T_IF);
    }
    /**
     * @return void
     * @param int $index
     */
    private function convertElseifToIf(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        if ($tokens[$index]->isGivenKind(\T_ELSE)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        } else {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_IF, 'if']);
        }
        $whitespace = '';
        for ($previous = $index - 1; $previous > 0; --$previous) {
            $token = $tokens[$previous];
            if ($token->isWhitespace() && \PhpCsFixer\Preg::match('/(\\R\\N*)$/', $token->getContent(), $matches)) {
                $whitespace = $matches[1];
                break;
            }
        }
        if ('' === $whitespace) {
            return;
        }
        $previousToken = $tokens[$index - 1];
        if (!$previousToken->isWhitespace()) {
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespace]));
        } elseif (!\PhpCsFixer\Preg::match('/\\R/', $previousToken->getContent())) {
            $tokens[$index - 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $whitespace]);
        }
    }
}
