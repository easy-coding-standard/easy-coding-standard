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
namespace PhpCsFixer\Fixer\ControlStructure;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Tokens;
final class ControlStructureContinuationPositionFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @internal
     */
    public const NEXT_LINE = 'next_line';
    /**
     * @internal
     */
    public const SAME_LINE = 'same_line';
    private const CONTROL_CONTINUATION_TOKENS = [\T_CATCH, \T_ELSE, \T_ELSEIF, \T_FINALLY, \T_WHILE];
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Control structure continuation keyword must be on the configured line.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
if ($baz == true) {
    echo "foo";
}
else {
    echo "bar";
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
if ($baz == true) {
    echo "foo";
} else {
    echo "bar";
}
', ['position' => self::NEXT_LINE])]);
    }
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound(self::CONTROL_CONTINUATION_TOKENS);
    }
    /**
     * Must run after ControlStructureBracesFixer.
     */
    public function getPriority() : int
    {
        return parent::getPriority();
    }
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('position', 'the position of the keyword that continues the control structure.'))->setAllowedValues([self::NEXT_LINE, self::SAME_LINE])->setDefault(self::SAME_LINE)->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $this->fixControlContinuationBraces($tokens);
    }
    private function fixControlContinuationBraces(\PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; 0 < $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(self::CONTROL_CONTINUATION_TOKENS)) {
                continue;
            }
            $prevIndex = $tokens->getPrevNonWhitespace($index);
            $prevToken = $tokens[$prevIndex];
            if (!$prevToken->equals('}')) {
                continue;
            }
            if ($token->isGivenKind(\T_WHILE)) {
                $prevIndex = $tokens->getPrevMeaningfulToken($tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $prevIndex));
                if (!$tokens[$prevIndex]->isGivenKind(\T_DO)) {
                    continue;
                }
            }
            $tokens->ensureWhitespaceAtIndex($index - 1, 1, self::NEXT_LINE === $this->configuration['position'] ? $this->whitespacesConfig->getLineEnding() . \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index) : ' ');
        }
    }
}
