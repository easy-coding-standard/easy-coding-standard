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
namespace PhpCsFixer;

use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @internal
 *
 * @author Vladimir Reznichenko <kalessil@gmail.com>
 */
abstract class AbstractFunctionReferenceFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * Looks up Tokens sequence for suitable candidates and delivers boundaries information,
     * which can be supplied by other methods in this abstract class.
     *
     * @return mixed[]|null returns $functionName, $openParenthesis, $closeParenthesis packed into array
     * @param int|null $end
     * @param string $functionNameToSearch
     * @param int $start
     */
    protected function find($functionNameToSearch, \PhpCsFixer\Tokenizer\Tokens $tokens, $start = 0, $end = null)
    {
        if (\is_object($functionNameToSearch)) {
            $functionNameToSearch = (string) $functionNameToSearch;
        }
        // make interface consistent with findSequence
        $end = null === $end ? $tokens->count() : $end;
        // find raw sequence which we can analyse for context
        $candidateSequence = [[\T_STRING, $functionNameToSearch], '('];
        $matches = $tokens->findSequence($candidateSequence, $start, $end, \false);
        if (null === $matches) {
            // not found, simply return without further attempts
            return null;
        }
        // translate results for humans
        list($functionName, $openParenthesis) = \array_keys($matches);
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $functionName)) {
            return $this->find($functionNameToSearch, $tokens, $openParenthesis, $end);
        }
        return [$functionName, $openParenthesis, $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis)];
    }
}
