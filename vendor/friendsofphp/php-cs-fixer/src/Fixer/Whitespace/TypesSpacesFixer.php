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
namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class TypesSpacesFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('A single space or none should be around union type operator.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\ntry\n{\n    new Foo();\n} catch (ErrorA | ErrorB \$e) {\necho'error';}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\ntry\n{\n    new Foo();\n} catch (ErrorA|ErrorB \$e) {\necho'error';}\n", ['space' => 'single']), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(int | string \$x)\n{\n}\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(80000))]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, \PhpCsFixer\Tokenizer\CT::T_TYPE_INTERSECTION]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space', 'spacing to apply around union type operator.'))->setAllowedValues(['none', 'single'])->setDefault('none')->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, \PhpCsFixer\Tokenizer\CT::T_TYPE_INTERSECTION])) {
                continue;
            }
            if ('single' === $this->configuration['space']) {
                $this->ensureSingleSpace($tokens, $index + 1, 0);
                $this->ensureSingleSpace($tokens, $index - 1, 1);
            } else {
                $this->ensureNoSpace($tokens, $index + 1);
                $this->ensureNoSpace($tokens, $index - 1);
            }
        }
    }
    private function ensureSingleSpace(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, int $offset) : void
    {
        if (!$tokens[$index]->isWhitespace()) {
            $tokens->insertSlices([$index + $offset => new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
            return;
        }
        if (' ' === $tokens[$index]->getContent()) {
            return;
        }
        if (1 === \PhpCsFixer\Preg::match('/\\R/', $tokens[$index]->getContent())) {
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
    }
    private function ensureNoSpace(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        if (!$tokens[$index]->isWhitespace()) {
            return;
        }
        if (1 === \PhpCsFixer\Preg::match('/\\R/', $tokens[$index]->getContent())) {
            return;
        }
        $tokens->clearAt($index);
    }
}
