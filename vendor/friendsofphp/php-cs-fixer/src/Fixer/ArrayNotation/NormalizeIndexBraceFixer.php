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
namespace PhpCsFixer\Fixer\ArrayNotation;

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
final class NormalizeIndexBraceFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Array index should always be written by using square braces.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\necho \$sample{\$index};\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN)) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token('[');
            } elseif ($token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE)) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token(']');
            }
        }
    }
}
