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
namespace PhpCsFixer\Fixer\ClassUsage;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class DateTimeImmutableFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Class `DateTimeImmutable` should be used instead of `DateTime`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nnew DateTime();\n")], null, 'Risky when the code relies on modifying `DateTime` objects or if any of the `date_create*` functions are overridden.');
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $isInNamespace = \false;
        $isImported = \false;
        // e.g. use DateTime;
        for ($index = 0, $limit = $tokens->count(); $index < $limit; ++$index) {
            $token = $tokens[$index];
            if ($token->isGivenKind(\T_NAMESPACE)) {
                $isInNamespace = \true;
                continue;
            }
            if ($token->isGivenKind(\T_USE) && $isInNamespace) {
                $nextIndex = $tokens->getNextMeaningfulToken($index);
                if ('datetime' !== \strtolower($tokens[$nextIndex]->getContent())) {
                    continue;
                }
                $nextNextIndex = $tokens->getNextMeaningfulToken($nextIndex);
                if ($tokens[$nextNextIndex]->equals(';')) {
                    $isImported = \true;
                }
                $index = $nextNextIndex;
                continue;
            }
            if (!$token->isGivenKind(\T_STRING)) {
                continue;
            }
            $lowercaseContent = \strtolower($token->getContent());
            if ('datetime' === $lowercaseContent) {
                $this->fixClassUsage($tokens, $index, $isInNamespace, $isImported);
                $limit = $tokens->count();
                // update limit, as fixing class usage may insert new token
            } elseif ('date_create' === $lowercaseContent) {
                $this->fixFunctionUsage($tokens, $index, 'date_create_immutable');
            } elseif ('date_create_from_format' === $lowercaseContent) {
                $this->fixFunctionUsage($tokens, $index, 'date_create_immutable_from_format');
            }
        }
    }
    /**
     * @return void
     * @param int $index
     * @param bool $isInNamespace
     * @param bool $isImported
     */
    private function fixClassUsage(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $isInNamespace, $isImported)
    {
        $nextIndex = $tokens->getNextMeaningfulToken($index);
        if ($tokens[$nextIndex]->isGivenKind(\T_DOUBLE_COLON)) {
            $nextNextIndex = $tokens->getNextMeaningfulToken($nextIndex);
            if ($tokens[$nextNextIndex]->isGivenKind(\T_STRING)) {
                $nextNextNextIndex = $tokens->getNextMeaningfulToken($nextNextIndex);
                if (!$tokens[$nextNextNextIndex]->equals('(')) {
                    return;
                }
            }
        }
        $isUsedAlone = \false;
        // e.g. new DateTime();
        $isUsedWithLeadingBackslash = \false;
        // e.g. new \DateTime();
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR)) {
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if (!$tokens[$prevPrevIndex]->isGivenKind(\T_STRING)) {
                $isUsedWithLeadingBackslash = \true;
            }
        } elseif (!$tokens[$prevIndex]->isGivenKind(\T_DOUBLE_COLON) && !$tokens[$prevIndex]->isObjectOperator()) {
            $isUsedAlone = \true;
        }
        if ($isUsedWithLeadingBackslash || $isUsedAlone && ($isInNamespace && $isImported || !$isInNamespace)) {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, \DateTimeImmutable::class]);
            if ($isInNamespace && $isUsedAlone) {
                $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']));
            }
        }
    }
    /**
     * @return void
     * @param int $index
     * @param string $replacement
     */
    private function fixFunctionUsage(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $replacement)
    {
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevIndex]->isGivenKind([\T_DOUBLE_COLON, \T_NEW]) || $tokens[$prevIndex]->isObjectOperator()) {
            return;
        }
        if ($tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR)) {
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if ($tokens[$prevPrevIndex]->isGivenKind([\T_NEW, \T_STRING])) {
                return;
            }
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $replacement]);
    }
}