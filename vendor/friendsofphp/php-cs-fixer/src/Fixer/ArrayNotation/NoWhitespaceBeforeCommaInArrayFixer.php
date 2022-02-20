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
use PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Options;
/**
 * @author Adam Marczuk <adam@marczuk.info>
 */
final class NoWhitespaceBeforeCommaInArrayFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('In array declaration, there MUST NOT be a whitespace before each comma.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \$x = array(1 , \"2\");\n"), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace ECSPrefix20220220;

$x = [<<<EOD
foo
EOD
, 'bar'];

SAMPLE
, new \PhpCsFixer\FixerDefinition\VersionSpecification(70300), ['after_heredoc' => \true])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokens[$index]->isGivenKind([\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                $this->fixSpacing($index, $tokens);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('after_heredoc', 'Whether the whitespace between heredoc end and comma should be removed.'))->setAllowedTypes(['bool'])->setDefault(\false)->setNormalizer(static function (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Options $options, $value) {
            if (\PHP_VERSION_ID < 70300 && $value) {
                throw new \PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException('"after_heredoc" option can only be enabled with PHP 7.3+.');
            }
            return $value;
        })->getOption()]);
    }
    /**
     * Method to fix spacing in array declaration.
     */
    private function fixSpacing(int $index, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        if ($tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
            $startIndex = $index;
            $endIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $startIndex);
        } else {
            $startIndex = $tokens->getNextTokenOfKind($index, ['(']);
            $endIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startIndex);
        }
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            $i = $this->skipNonArrayElements($i, $tokens);
            $currentToken = $tokens[$i];
            $prevIndex = $tokens->getPrevNonWhitespace($i - 1);
            if ($currentToken->equals(',') && !$tokens[$prevIndex]->isComment() && (\true === $this->configuration['after_heredoc'] || !$tokens[$prevIndex]->equals([\T_END_HEREDOC]))) {
                $tokens->removeLeadingWhitespace($i);
            }
        }
    }
    /**
     * Method to move index over the non-array elements like function calls or function declarations.
     *
     * @return int New index
     */
    private function skipNonArrayElements(int $index, \PhpCsFixer\Tokenizer\Tokens $tokens) : int
    {
        if ($tokens[$index]->equals('}')) {
            return $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        }
        if ($tokens[$index]->equals(')')) {
            $startIndex = $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index);
            $startIndex = $tokens->getPrevMeaningfulToken($startIndex);
            if (!$tokens[$startIndex]->isGivenKind([\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN])) {
                return $startIndex;
            }
        }
        return $index;
    }
}
