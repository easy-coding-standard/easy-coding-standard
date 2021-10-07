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
namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for rules defined in PSR2 ¶2.5.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class LowercaseKeywordsFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * @var int[]
     */
    private static $excludedTokens = [\T_HALT_COMPILER];
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('PHP keywords MUST be in lower case.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
    FOREACH($a AS $B) {
        TRY {
            NEW $C($a, ISSET($B));
            WHILE($B) {
                INCLUDE "test.php";
            }
        } CATCH(\\Exception $e) {
            EXIT(1);
        }
    }
')]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound(\PhpCsFixer\Tokenizer\Token::getKeywords());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if ($token->isKeyword() && !$token->isGivenKind(self::$excludedTokens)) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), \strtolower($token->getContent())]);
            }
        }
    }
}
