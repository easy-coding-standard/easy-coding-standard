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
final class ShortScalarCastFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Cast `(boolean)` and `(integer)` should be written as `(bool)` and `(int)`, `(double)` and `(real)` as `(float)`, `(binary)` as `(string)`.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n\$a = (boolean) \$b;\n\$a = (integer) \$b;\n\$a = (double) \$b;\n\$a = (real) \$b;\n\n\$a = (binary) \$b;\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(null, 70399)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n\$a = (boolean) \$b;\n\$a = (integer) \$b;\n\$a = (double) \$b;\n\n\$a = (binary) \$b;\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70400))]);
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
        static $castMap = ['boolean' => 'bool', 'integer' => 'int', 'double' => 'float', 'real' => 'float', 'binary' => 'string'];
        for ($index = 0, $count = $tokens->count(); $index < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }
            $castFrom = \trim(\substr($tokens[$index]->getContent(), 1, -1));
            $castFromLowered = \strtolower($castFrom);
            if (!\array_key_exists($castFromLowered, $castMap)) {
                continue;
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), \str_replace($castFrom, $castMap[$castFromLowered], $tokens[$index]->getContent())]);
        }
    }
}
