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
namespace PhpCsFixer\Fixer\ArrayNotation;

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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan <gharlan@web.de>
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class ArraySyntaxFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    private $candidateTokenKind;
    private $fixCallback;
    /**
     * {@inheritdoc}
     * @return void
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);
        $this->resolveCandidateTokenKind();
        $this->resolveFixCallback();
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('PHP arrays should be declared using the configured syntax.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\narray(1,2);\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(50400)), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n[1,2];\n", ['syntax' => 'long'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer, TernaryOperatorSpacesFixer.
     */
    public function getPriority() : int
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound($this->candidateTokenKind);
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $callback = $this->fixCallback;
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if ($tokens[$index]->isGivenKind($this->candidateTokenKind)) {
                $this->{$callback}($tokens, $index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('syntax', 'Whether to use the `long` or `short` array syntax.'))->setAllowedValues(['long', 'short'])->setDefault('short')->getOption()]);
    }
    /**
     * @return void
     */
    private function fixToLongArraySyntax(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token('(');
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token(')');
        $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_ARRAY, 'array']));
    }
    /**
     * @return void
     */
    private function fixToShortArraySyntax(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        $openIndex = $tokens->getNextTokenOfKind($index, ['(']);
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
        $tokens[$openIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, '[']);
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_CLOSE, ']']);
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
    }
    /**
     * @return void
     */
    private function resolveFixCallback()
    {
        $this->fixCallback = \sprintf('fixTo%sArraySyntax', \ucfirst($this->configuration['syntax']));
    }
    /**
     * @return void
     */
    private function resolveCandidateTokenKind()
    {
        $this->candidateTokenKind = 'long' === $this->configuration['syntax'] ? \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN : \T_ARRAY;
    }
}
