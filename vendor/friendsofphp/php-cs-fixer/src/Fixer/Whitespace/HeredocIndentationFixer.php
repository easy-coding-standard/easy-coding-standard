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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan
 */
final class HeredocIndentationFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Heredoc/nowdoc content must be properly indented. Requires PHP >= 7.3.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace ECSPrefix20220220;

$a = <<<EOD
abc
    def
EOD
;

SAMPLE
, new \PhpCsFixer\FixerDefinition\VersionSpecification(70300)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace ECSPrefix20220220;

$a = <<<'EOD'
abc
    def
EOD
;

SAMPLE
, new \PhpCsFixer\FixerDefinition\VersionSpecification(70300)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample(<<<'SAMPLE'
<?php

namespace ECSPrefix20220220;

$a = <<<'EOD'
abc
    def
EOD
;

SAMPLE
, new \PhpCsFixer\FixerDefinition\VersionSpecification(70300), ['indentation' => 'same_as_start'])]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return \PHP_VERSION_ID >= 70300 && $tokens->isTokenKindFound(\T_START_HEREDOC);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('indentation', 'Whether the indentation should be the same as in the start token line or one level more.'))->setAllowedValues(['start_plus_one', 'same_as_start'])->setDefault('start_plus_one')->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_END_HEREDOC)) {
                continue;
            }
            $end = $index;
            $index = $tokens->getPrevTokenOfKind($index, [[\T_START_HEREDOC]]);
            $this->fixIndentation($tokens, $index, $end);
        }
    }
    private function fixIndentation(\PhpCsFixer\Tokenizer\Tokens $tokens, int $start, int $end) : void
    {
        $indent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $start);
        if ('start_plus_one' === $this->configuration['indentation']) {
            $indent .= $this->whitespacesConfig->getIndent();
        }
        \PhpCsFixer\Preg::match('/^\\h*/', $tokens[$end]->getContent(), $matches);
        $currentIndent = $matches[0];
        $currentIndentLength = \strlen($currentIndent);
        $content = $indent . \substr($tokens[$end]->getContent(), $currentIndentLength);
        $tokens[$end] = new \PhpCsFixer\Tokenizer\Token([\T_END_HEREDOC, $content]);
        if ($end === $start + 1) {
            return;
        }
        for ($index = $end - 1, $last = \true; $index > $start; --$index, $last = \false) {
            if (!$tokens[$index]->isGivenKind([\T_ENCAPSED_AND_WHITESPACE, \T_WHITESPACE])) {
                continue;
            }
            $content = $tokens[$index]->getContent();
            if ('' !== $currentIndent) {
                $content = \PhpCsFixer\Preg::replace('/(?<=\\v)(?!' . $currentIndent . ')\\h+/', '', $content);
            }
            $regexEnd = $last && !$currentIndent ? '(?!\\v|$)' : '(?!\\v)';
            $content = \PhpCsFixer\Preg::replace('/(?<=\\v)' . $currentIndent . $regexEnd . '/', $indent, $content);
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), $content]);
        }
        ++$index;
        if (!$tokens[$index]->isGivenKind(\T_ENCAPSED_AND_WHITESPACE)) {
            $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_ENCAPSED_AND_WHITESPACE, $indent]));
            return;
        }
        $content = $tokens[$index]->getContent();
        if (!\in_array($content[0], ["\r", "\n"], \true) && (!$currentIndent || $currentIndent === \substr($content, 0, $currentIndentLength))) {
            $content = $indent . \substr($content, $currentIndentLength);
        } elseif ($currentIndent) {
            $content = \PhpCsFixer\Preg::replace('/^(?!' . $currentIndent . ')\\h+/', '', $content);
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_ENCAPSED_AND_WHITESPACE, $content]);
    }
}
