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
namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class PhpdocLineSpanFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Changes doc blocks from single to multi line, or reversed. Works for class constants, properties and methods only.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nclass Foo{\n    /** @var bool */\n    public \$var;\n}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nclass Foo{\n    /**\n    * @var bool\n    */\n    public \$var;\n}\n", ['property' => 'single'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, GeneralPhpdocAnnotationRemoveFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
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
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('const', 'Whether const blocks should be single or multi line'))->setAllowedValues(['single', 'multi'])->setDefault('multi')->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('property', 'Whether property doc blocks should be single or multi line'))->setAllowedValues(['single', 'multi'])->setDefault('multi')->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('method', 'Whether method doc blocks should be single or multi line'))->setAllowedValues(['single', 'multi'])->setDefault('multi')->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $analyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $elements = $analyzer->getClassyElements();
        foreach ($elements as $index => $element) {
            if (!$this->hasDocBlock($tokens, $index)) {
                continue;
            }
            $type = $element['type'];
            $docIndex = $this->getDocBlockIndex($tokens, $index);
            $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$docIndex]->getContent());
            if ('multi' === $this->configuration[$type]) {
                $doc->makeMultiLine($originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $docIndex), $this->whitespacesConfig->getLineEnding());
            } else {
                $doc->makeSingleLine();
            }
            $tokens->offsetSet($docIndex, new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]));
        }
    }
    private function hasDocBlock(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $docBlockIndex = $this->getDocBlockIndex($tokens, $index);
        return $tokens[$docBlockIndex]->isGivenKind(\T_DOC_COMMENT);
    }
    private function getDocBlockIndex(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : int
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_FINAL, \T_ABSTRACT, \T_COMMENT, \T_VAR, \T_STATIC, \T_STRING, \T_NS_SEPARATOR, \PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, \PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE]));
        return $index;
    }
}
