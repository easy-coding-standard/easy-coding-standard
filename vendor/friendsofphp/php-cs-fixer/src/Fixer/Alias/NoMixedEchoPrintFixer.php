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
namespace PhpCsFixer\Fixer\Alias;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Sullivan Senechal <soullivaneuh@gmail.com>
 * @author SpacePossum
 */
final class NoMixedEchoPrintFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @var string
     */
    private $callBack;
    /**
     * @var int T_ECHO or T_PRINT
     */
    private $candidateTokenType;
    /**
     * {@inheritdoc}
     * @param mixed[] $configuration
     * @return void
     */
    public function configure($configuration)
    {
        parent::configure($configuration);
        if ('echo' === $this->configuration['use']) {
            $this->candidateTokenType = \T_PRINT;
            $this->callBack = 'fixPrintToEcho';
        } else {
            $this->candidateTokenType = \T_ECHO;
            $this->callBack = 'fixEchoToPrint';
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Either language construct `print` or `echo` should be used.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php print 'example';\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php echo('example');\n", ['use' => 'print'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after EchoTagSyntaxFixer.
     */
    public function getPriority() : int
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    public function isCandidate($tokens) : bool
    {
        return $tokens->isTokenKindFound($this->candidateTokenType);
    }
    /**
     * {@inheritdoc}
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return void
     */
    protected function applyFix($file, $tokens)
    {
        $callBack = $this->callBack;
        foreach ($tokens as $index => $token) {
            if ($token->isGivenKind($this->candidateTokenType)) {
                $this->{$callBack}($tokens, $index);
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('use', 'The desired language construct.'))->setAllowedValues(['print', 'echo'])->setDefault('echo')->getOption()]);
    }
    /**
     * @return void
     */
    private function fixEchoToPrint(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
        $endTokenIndex = $tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
        $canBeConverted = \true;
        for ($i = $nextTokenIndex; $i < $endTokenIndex; ++$i) {
            if ($tokens[$i]->equalsAny(['(', [\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]])) {
                $blockType = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($tokens[$i]);
                $i = $tokens->findBlockEnd($blockType['type'], $i);
            }
            if ($tokens[$i]->equals(',')) {
                $canBeConverted = \false;
                break;
            }
        }
        if (\false === $canBeConverted) {
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_PRINT, 'print']);
    }
    /**
     * @return void
     */
    private function fixPrintToEcho(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index)
    {
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
        if (!$prevToken->equalsAny([';', '{', '}', ')', [\T_OPEN_TAG], [\T_ELSE]])) {
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_ECHO, 'echo']);
    }
}
