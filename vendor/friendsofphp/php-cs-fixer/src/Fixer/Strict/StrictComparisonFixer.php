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
namespace PhpCsFixer\Fixer\Strict;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StrictComparisonFixer extends \PhpCsFixer\AbstractFixer
{
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Comparisons should be strict.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = 1== \$b;\n")], null, 'Changing comparisons to strict might change code behavior.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_IS_EQUAL, \T_IS_NOT_EQUAL]);
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky() : bool
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        static $map = [\T_IS_EQUAL => ['id' => \T_IS_IDENTICAL, 'content' => '==='], \T_IS_NOT_EQUAL => ['id' => \T_IS_NOT_IDENTICAL, 'content' => '!==']];
        foreach ($tokens as $index => $token) {
            $tokenId = $token->getId();
            if (isset($map[$tokenId])) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$map[$tokenId]['id'], $map[$tokenId]['content']]);
            }
        }
    }
}
