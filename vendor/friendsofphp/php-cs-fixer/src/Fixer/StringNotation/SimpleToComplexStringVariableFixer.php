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
namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dave van der Brugge <dmvdbrugge@gmail.com>
 */
final class SimpleToComplexStringVariableFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Converts explicit variables in double-quoted strings and heredoc syntax from simple to complex format (`${` to `{$`).', [new \PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace ECSPrefix20220220;

$name = 'World';
echo "Hello {$name}!";

EOT
), new \PhpCsFixer\FixerDefinition\CodeSample(<<<'EOT'
<?php

namespace ECSPrefix20220220;

$name = 'World';
echo <<<TEST
Hello {$name}!
TEST
;

EOT
)], "Doesn't touch implicit variables. Works together nicely with `explicit_string_variable`.");
    }
    /**
     * {@inheritdoc}
     *
     * Must run after ExplicitStringVariableFixer.
     */
    public function getPriority() : int
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOLLAR_OPEN_CURLY_BRACES);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 3; $index > 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_DOLLAR_OPEN_CURLY_BRACES)) {
                continue;
            }
            $varnameToken = $tokens[$index + 1];
            if (!$varnameToken->isGivenKind(\T_STRING_VARNAME)) {
                continue;
            }
            $dollarCloseToken = $tokens[$index + 2];
            if (!$dollarCloseToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_DOLLAR_CLOSE_CURLY_BRACES)) {
                continue;
            }
            $tokenOfStringBeforeToken = $tokens[$index - 1];
            $stringContent = $tokenOfStringBeforeToken->getContent();
            if (\substr_compare($stringContent, '$', -\strlen('$')) === 0 && \substr_compare($stringContent, '\\$', -\strlen('\\$')) !== 0) {
                $newContent = \substr($stringContent, 0, -1) . '\\$';
                $tokenOfStringBeforeToken = new \PhpCsFixer\Tokenizer\Token([\T_ENCAPSED_AND_WHITESPACE, $newContent]);
            }
            $tokens->overrideRange($index - 1, $index + 2, [$tokenOfStringBeforeToken, new \PhpCsFixer\Tokenizer\Token([\T_CURLY_OPEN, '{']), new \PhpCsFixer\Tokenizer\Token([\T_VARIABLE, '$' . $varnameToken->getContent()]), new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE, '}'])]);
        }
    }
}
