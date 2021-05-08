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
namespace PhpCsFixer\Fixer\CastNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class CastSpacesFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    const INSIDE_CAST_SPACE_REPLACE_MAP = [' ' => '', "\t" => '', "\n" => '', "\r" => '', "\0" => '', "\v" => ''];
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('A single space or none should be between cast and variable.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$bar = ( string )  \$a;\n\$foo = (int)\$b;\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$bar = ( string )  \$a;\n\$foo = (int)\$b;\n", ['space' => 'single']), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$bar = ( string )  \$a;\n\$foo = (int) \$b;\n", ['space' => 'none'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after NoShortBoolCastFixer.
     * @return int
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(\PhpCsFixer\Tokenizer\Token::getCastTokenKinds());
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isCast()) {
                continue;
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), \strtr($token->getContent(), self::INSIDE_CAST_SPACE_REPLACE_MAP)]);
            if ('single' === $this->configuration['space']) {
                // force single whitespace after cast token:
                if ($tokens[$index + 1]->isWhitespace(" \t")) {
                    // - if next token is whitespaces that contains only spaces and tabs - override next token with single space
                    $tokens[$index + 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
                } elseif (!$tokens[$index + 1]->isWhitespace()) {
                    // - if next token is not whitespaces that contains spaces, tabs and new lines - append single space to current token
                    $tokens->insertAt($index + 1, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
                }
                continue;
            }
            // force no whitespace after cast token:
            if ($tokens[$index + 1]->isWhitespace()) {
                $tokens->clearAt($index + 1);
            }
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('space', 'spacing to apply between cast and variable.'))->setAllowedValues(['none', 'single'])->setDefault('single')->getOption()]);
    }
}
