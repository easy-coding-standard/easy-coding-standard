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
namespace PhpCsFixer\Fixer\Basic;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class OctalNotationFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Literal octal must be in `0o` notation.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php \$foo = 0123;\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(80100))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return \PHP_VERSION_ID >= 80100 && $tokens->isTokenKindFound(\T_LNUMBER);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_LNUMBER)) {
                continue;
            }
            $content = $token->getContent();
            if (1 !== \PhpCsFixer\Preg::match('#^0\\d+$#', $content)) {
                continue;
            }
            $tokens[$index] = 1 === \PhpCsFixer\Preg::match('#^0+$#', $content) ? new \PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '0']) : new \PhpCsFixer\Tokenizer\Token([\T_LNUMBER, '0o' . \substr($content, 1)]);
        }
    }
}
