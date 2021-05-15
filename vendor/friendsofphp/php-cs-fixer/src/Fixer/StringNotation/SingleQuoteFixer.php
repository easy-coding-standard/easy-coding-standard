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
namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class SingleQuoteFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        $codeSample = <<<'EOF'
<?php

namespace ECSPrefix20210515;

$a = "sample";
$b = "sample with 'single-quotes'";

EOF;
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Convert double quotes to single quotes for simple strings.', [new \PhpCsFixer\FixerDefinition\CodeSample($codeSample), new \PhpCsFixer\FixerDefinition\CodeSample($codeSample, ['strings_containing_single_quote_chars' => \true])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after BacktickToShellExecFixer, EscapeImplicitBackslashesFixer.
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_CONSTANT_ENCAPSED_STRING);
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_CONSTANT_ENCAPSED_STRING)) {
                continue;
            }
            $content = $token->getContent();
            $prefix = '';
            if ('b' === \strtolower($content[0])) {
                $prefix = $content[0];
                $content = \substr($content, 1);
            }
            if ('"' === $content[0] && (\true === $this->configuration['strings_containing_single_quote_chars'] || \false === \strpos($content, "'")) && !\PhpCsFixer\Preg::match('/(?<!\\\\)(?:\\\\{2})*\\\\(?!["$\\\\])/', $content)) {
                $content = \substr($content, 1, -1);
                $content = \str_replace(['\\"', '\\$', '\''], ['"', '$', '\\\''], $content);
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, $prefix . '\'' . $content . '\'']);
            }
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('strings_containing_single_quote_chars', 'Whether to fix double-quoted strings that contains single-quotes.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]);
    }
}
