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
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
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
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Options;
/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class TrailingCommaInMultilineFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @internal
     */
    public const ELEMENTS_ARRAYS = 'arrays';
    /**
     * @internal
     */
    public const ELEMENTS_ARGUMENTS = 'arguments';
    /**
     * @internal
     */
    public const ELEMENTS_PARAMETERS = 'parameters';
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Multi-line arrays, arguments list and parameters list must have a trailing comma.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\narray(\n    1,\n    2\n);\n"), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace ECSPrefix20220220;

$x = ['foo', <<<EOD
bar
EOD
];

SAMPLE
, new \PhpCsFixer\FixerDefinition\VersionSpecification(70300), ['after_heredoc' => \true]), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfoo(\n    1,\n    2\n);\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70300), ['elements' => [self::ELEMENTS_ARGUMENTS]]), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(\n    \$x,\n    \$y\n)\n{\n}\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(80000), ['elements' => [self::ELEMENTS_PARAMETERS]])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoMultilineWhitespaceAroundDoubleArrowFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN, '(']);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('after_heredoc', 'Whether a trailing comma should also be placed after heredoc end.'))->setAllowedTypes(['bool'])->setDefault(\false)->setNormalizer(static function (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Options $options, $value) {
            if (\PHP_VERSION_ID < 70300 && $value) {
                throw new \PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException('"after_heredoc" option can only be enabled with PHP 7.3+.');
            }
            return $value;
        })->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('elements', \sprintf('Where to fix multiline trailing comma (PHP >= 7.3 required for `%s`, PHP >= 8.0 for `%s`).', self::ELEMENTS_ARGUMENTS, self::ELEMENTS_PARAMETERS)))->setAllowedTypes(['array'])->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset([self::ELEMENTS_ARRAYS, self::ELEMENTS_ARGUMENTS, self::ELEMENTS_PARAMETERS])])->setDefault([self::ELEMENTS_ARRAYS])->setNormalizer(static function (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Options $options, $value) {
            if (\PHP_VERSION_ID < 70300 && \in_array(self::ELEMENTS_ARGUMENTS, $value, \true)) {
                throw new \PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException(\sprintf('"%s" option can only be enabled with PHP 7.3+.', self::ELEMENTS_ARGUMENTS));
            }
            if (\PHP_VERSION_ID < 80000 && \in_array(self::ELEMENTS_PARAMETERS, $value, \true)) {
                throw new \PhpCsFixer\FixerConfiguration\InvalidOptionsForEnvException(\sprintf('"%s" option can only be enabled with PHP 8.0+.', self::ELEMENTS_PARAMETERS));
            }
            return $value;
        })->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $fixArrays = \in_array(self::ELEMENTS_ARRAYS, $this->configuration['elements'], \true);
        $fixArguments = \in_array(self::ELEMENTS_ARGUMENTS, $this->configuration['elements'], \true);
        $fixParameters = \in_array(self::ELEMENTS_PARAMETERS, $this->configuration['elements'], \true);
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            if ($fixArrays && ($tokens[$index]->equals('(') && $tokens[$prevIndex]->isGivenKind(\T_ARRAY) || $tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN))) {
                $this->fixBlock($tokens, $index);
                continue;
            }
            if (!$tokens[$index]->equals('(')) {
                continue;
            }
            $prevPrevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            if ($fixArguments && $tokens[$prevIndex]->equalsAny([']', [\T_CLASS], [\T_STRING], [\T_VARIABLE]]) && !$tokens[$prevPrevIndex]->isGivenKind(\T_FUNCTION)) {
                $this->fixBlock($tokens, $index);
                continue;
            }
            if ($fixParameters && ($tokens[$prevIndex]->isGivenKind(\T_STRING) && $tokens[$prevPrevIndex]->isGivenKind(\T_FUNCTION) || $tokens[$prevIndex]->isGivenKind([\T_FN, \T_FUNCTION]))) {
                $this->fixBlock($tokens, $index);
            }
        }
    }
    private function fixBlock(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex) : void
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        if (!$tokensAnalyzer->isBlockMultiline($tokens, $startIndex)) {
            return;
        }
        $blockType = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($tokens[$startIndex]);
        $endIndex = $tokens->findBlockEnd($blockType['type'], $startIndex);
        $beforeEndIndex = $tokens->getPrevMeaningfulToken($endIndex);
        $beforeEndToken = $tokens[$beforeEndIndex];
        // if there is some item between braces then add `,` after it
        if ($startIndex !== $beforeEndIndex && !$beforeEndToken->equals(',') && (\true === $this->configuration['after_heredoc'] || !$beforeEndToken->isGivenKind(\T_END_HEREDOC))) {
            $tokens->insertAt($beforeEndIndex + 1, new \PhpCsFixer\Tokenizer\Token(','));
            $endToken = $tokens[$endIndex];
            if (!$endToken->isComment() && !$endToken->isWhitespace()) {
                $tokens->ensureWhitespaceAtIndex($endIndex, 1, ' ');
            }
        }
    }
}
