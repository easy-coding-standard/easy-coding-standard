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

use PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 */
final class SwitchAnalyzer
{
    public function getSwitchAnalysis(\PhpCsFixer\Tokenizer\Tokens $tokens, int $switchIndex) : \PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis
    {
        if (!$tokens[$switchIndex]->isGivenKind(\T_SWITCH)) {
            throw new \InvalidArgumentException(\sprintf('Index %d is not "switch".', $switchIndex));
        }
        $casesStartIndex = $this->getCasesStart($tokens, $switchIndex);
        $casesEndIndex = $this->getCasesEnd($tokens, $casesStartIndex);
        $cases = [];
        $index = $casesStartIndex;
        while ($index < $casesEndIndex) {
            $index = $this->getNextSameLevelToken($tokens, $index);
            if (!$tokens[$index]->isGivenKind([\T_CASE, \T_DEFAULT])) {
                continue;
            }
            $caseAnalysis = $this->getCaseAnalysis($tokens, $index);
            $cases[] = $caseAnalysis;
        }
        return new \PhpCsFixer\Tokenizer\Analyzer\Analysis\SwitchAnalysis($casesStartIndex, $casesEndIndex, $cases);
    }
    private function getCasesStart(\PhpCsFixer\Tokenizer\Tokens $tokens, int $switchIndex) : int
    {
        /** @var int $parenthesisStartIndex */
        $parenthesisStartIndex = $tokens->getNextMeaningfulToken($switchIndex);
        $parenthesisEndIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $parenthesisStartIndex);
        $casesStartIndex = $tokens->getNextMeaningfulToken($parenthesisEndIndex);
        \assert(\is_int($casesStartIndex));
        return $casesStartIndex;
    }
    private function getCasesEnd(\PhpCsFixer\Tokenizer\Tokens $tokens, int $casesStartIndex) : int
    {
        if ($tokens[$casesStartIndex]->equals('{')) {
            return $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $casesStartIndex);
        }
        $index = $casesStartIndex;
        while ($index < $tokens->count()) {
            $index = $this->getNextSameLevelToken($tokens, $index);
            if ($tokens[$index]->isGivenKind(\T_ENDSWITCH)) {
                break;
            }
        }
        $afterEndswitchIndex = $tokens->getNextMeaningfulToken($index);
        $afterEndswitchToken = $tokens[$afterEndswitchIndex];
        return $afterEndswitchToken->equalsAny([';', [\T_CLOSE_TAG]]) ? $afterEndswitchIndex : $index;
    }
    private function getCaseAnalysis(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : \PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis
    {
        while ($index < $tokens->count()) {
            $index = $this->getNextSameLevelToken($tokens, $index);
            if ($tokens[$index]->equalsAny([':', ';'])) {
                break;
            }
        }
        return new \PhpCsFixer\Tokenizer\Analyzer\Analysis\CaseAnalysis($index);
    }
    private function getNextSameLevelToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : int
    {
        $index = $tokens->getNextMeaningfulToken($index);
        if ($tokens[$index]->isGivenKind(\T_SWITCH)) {
            return (new self())->getSwitchAnalysis($tokens, $index)->getCasesEnd();
        }
        /** @var null|array{isStart: bool, type: int} $blockType */
        $blockType = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($tokens[$index]);
        if (null !== $blockType && $blockType['isStart']) {
            return $tokens->findBlockEnd($blockType['type'], $index) + 1;
        }
        return $index;
    }
}
