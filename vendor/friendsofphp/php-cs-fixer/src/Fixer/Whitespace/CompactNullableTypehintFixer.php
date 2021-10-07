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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Jack Cherng <jfcherng@gmail.com>
 */
final class CompactNullableTypehintFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Remove extra spaces in a nullable typehint.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(? string \$str): ? string\n{}\n")], 'Rule is applied only in a PHP 7.1+ environment.');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        static $typehintKinds = [\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, \T_CALLABLE, \T_NS_SEPARATOR, \T_STRING];
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if (!$tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE)) {
                continue;
            }
            // remove whitespaces only if there are only whitespaces
            // between '?' and the variable type
            if ($tokens[$index + 1]->isWhitespace() && $tokens[$index + 2]->isGivenKind($typehintKinds)) {
                $tokens->removeTrailingWhitespace($index);
            }
        }
    }
}
