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
    public function configure(array $configuration) : void
    {
        parent::configure($configuration);
        if (!isset($this->configuration['space_multiple_catch'])) {
            $this->configuration['space_multiple_catch'] = $this->configuration['space'];
        }
    }
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
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space', 'spacing to apply around union type operator.'))->setAllowedValues(['none', 'single'])->setDefault('none')->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space_multiple_catch', 'spacing to apply around type operator when catching exceptions of multiple types, use `null` to follow the value configured for `space`.'))->setAllowedValues(['none', 'single', null])->setDefault(null)->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $tokenCount = $tokens->count() - 1;
        for ($index = 0; $index < $tokenCount; ++$index) {
            if ($tokens[$index]->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, \PhpCsFixer\Tokenizer\CT::T_TYPE_INTERSECTION])) {
                $tokenCount += $this->fixSpacing($tokens, $index, 'single' === $this->configuration['space']);
                continue;
            }
            if ($tokens[$index]->isGivenKind(\T_CATCH)) {
                while (\true) {
                    $index = $tokens->getNextTokenOfKind($index, [')', [\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION]]);
                    if ($tokens[$index]->equals(')')) {
                        break;
                    }
                    $tokenCount += $this->fixSpacing($tokens, $index, 'single' === $this->configuration['space_multiple_catch']);
                }
                // implicit continue
            }
        }
    }
    private function fixSpacing(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, bool $singleSpace) : int
    {
        if (!$singleSpace) {
            $this->ensureNoSpace($tokens, $index + 1);
            $this->ensureNoSpace($tokens, $index - 1);
            return 0;
        }
        $addedTokenCount = 0;
        $addedTokenCount += $this->ensureSingleSpace($tokens, $index + 1, 0);
        $addedTokenCount += $this->ensureSingleSpace($tokens, $index - 1, 1);
        return $addedTokenCount;
    }
    private function ensureSingleSpace(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, int $offset) : int
    {
        if (!$tokens[$index]->isWhitespace()) {
            $tokens->insertSlices([$index + $offset => new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
            return 1;
        }
        if (' ' !== $tokens[$index]->getContent() && 1 !== \PhpCsFixer\Preg::match('/\\R/', $tokens[$index]->getContent())) {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
        }
        return 0;
    }
    private function ensureNoSpace(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        if ($tokens[$index]->isWhitespace() && 1 !== \PhpCsFixer\Preg::match('/\\R/', $tokens[$index]->getContent())) {
            $tokens->clearAt($index);
        }
    }
}
