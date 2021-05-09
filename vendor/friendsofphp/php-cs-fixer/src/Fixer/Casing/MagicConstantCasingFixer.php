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
namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author ntzm
 */
final class MagicConstantCasingFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Magic constants should be referred to using the correct casing.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\necho __dir__;\n")]);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound($this->getMagicConstantTokens());
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $magicConstants = $this->getMagicConstants();
        $magicConstantTokens = $this->getMagicConstantTokens();
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($magicConstantTokens)) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $magicConstants[$token->getId()]]);
            }
        }
    }
    /**
     * @return mixed[]
     */
    private function getMagicConstants()
    {
        static $magicConstants = null;
        if (null === $magicConstants) {
            $magicConstants = [\T_LINE => '__LINE__', \T_FILE => '__FILE__', \T_DIR => '__DIR__', \T_FUNC_C => '__FUNCTION__', \T_CLASS_C => '__CLASS__', \T_METHOD_C => '__METHOD__', \T_NS_C => '__NAMESPACE__', \PhpCsFixer\Tokenizer\CT::T_CLASS_CONSTANT => 'class', \T_TRAIT_C => '__TRAIT__'];
        }
        return $magicConstants;
    }
    /**
     * @return mixed[]
     */
    private function getMagicConstantTokens()
    {
        static $magicConstantTokens = null;
        if (null === $magicConstantTokens) {
            $magicConstantTokens = \array_keys($this->getMagicConstants());
        }
        return $magicConstantTokens;
    }
}
