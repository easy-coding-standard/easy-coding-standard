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
namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class ExplicitIndirectVariableFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Add curly braces to indirect variables to make them clear to understand. Requires PHP >= 7.0.', [new \PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace ECSPrefix20220220;

echo ${$foo};
echo ${$foo}['bar'];
echo $foo->{$bar}['baz'];
echo $foo->{$callback}($baz);

EOT
)]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_VARIABLE);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index > 1; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_VARIABLE)) {
                continue;
            }
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $prevToken = $tokens[$prevIndex];
            if (!$prevToken->equals('$') && !$prevToken->isObjectOperator()) {
                continue;
            }
            $openingBrace = \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_OPEN;
            $closingBrace = \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE;
            if ($prevToken->isObjectOperator()) {
                $openingBrace = \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_OPEN;
                $closingBrace = \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_CLOSE;
            }
            $tokens->overrideRange($index, $index, [new \PhpCsFixer\Tokenizer\Token([$openingBrace, '{']), new \PhpCsFixer\Tokenizer\Token([\T_VARIABLE, $token->getContent()]), new \PhpCsFixer\Tokenizer\Token([$closingBrace, '}'])]);
        }
    }
}
