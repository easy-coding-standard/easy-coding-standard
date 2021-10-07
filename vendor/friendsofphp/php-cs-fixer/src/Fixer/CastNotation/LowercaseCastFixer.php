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
namespace PhpCsFixer\Fixer\CastNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class LowercaseCastFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Cast should be written in lower case.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (reaL) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new \PhpCsFixer\FixerDefinition\VersionSpecification(null, 70399)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
    $a = (BOOLEAN) $b;
    $a = (BOOL) $b;
    $a = (INTEGER) $b;
    $a = (INT) $b;
    $a = (DOUBLE) $b;
    $a = (FLoaT) $b;
    $a = (flOAT) $b;
    $a = (sTRING) $b;
    $a = (ARRAy) $b;
    $a = (OBJect) $b;
    $a = (UNset) $b;
    $a = (Binary) $b;
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70400))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound(\PhpCsFixer\Tokenizer\Token::getCastTokenKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), \strtolower($tokens[$index]->getContent())]);
        }
    }
}
