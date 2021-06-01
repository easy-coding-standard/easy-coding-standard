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
namespace PhpCsFixer\Tokenizer\Analyzer;

use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class ReferenceAnalyzer
{
    public function isReference(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        if ($tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_RETURN_REF)) {
            return \true;
        }
        if (!$tokens[$index]->equals('&')) {
            return \false;
        }
        /** @var int $index */
        $index = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$index]->equalsAny(['=', [\T_AS], [\T_CALLABLE], [\T_DOUBLE_ARROW], [\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT]])) {
            return \true;
        }
        if ($tokens[$index]->isGivenKind(\T_STRING)) {
            $index = $tokens->getPrevMeaningfulToken($index);
        }
        return $tokens[$index]->equalsAny(['(', ',', [\T_NS_SEPARATOR], [\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE]]);
    }
}
