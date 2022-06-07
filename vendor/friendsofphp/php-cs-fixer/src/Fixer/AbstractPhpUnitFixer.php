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
namespace PhpCsFixer\Fixer;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Indicator\PhpUnitTestCaseIndicator;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 */
abstract class AbstractPhpUnitFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public final function isCandidate(Tokens $tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_STRING]);
    }
    protected final function applyFix(\SplFileInfo $file, Tokens $tokens) : void
    {
        $phpUnitTestCaseIndicator = new PhpUnitTestCaseIndicator();
        foreach ($phpUnitTestCaseIndicator->findPhpUnitClasses($tokens) as $indices) {
            $this->applyPhpUnitClassFix($tokens, $indices[0], $indices[1]);
        }
    }
    protected abstract function applyPhpUnitClassFix(Tokens $tokens, int $startIndex, int $endIndex) : void;
    protected final function getDocBlockIndex(Tokens $tokens, int $index) : int
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT]));
        return $index;
    }
    protected final function isPHPDoc(Tokens $tokens, int $index) : bool
    {
        return $tokens[$index]->isGivenKind(\T_DOC_COMMENT);
    }
}
