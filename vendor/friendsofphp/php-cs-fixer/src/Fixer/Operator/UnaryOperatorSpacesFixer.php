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
namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gregor Harlan <gharlan@web.de>
 */
final class UnaryOperatorSpacesFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Unary operators should be placed adjacent to their operands.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$sample ++;\n-- \$sample;\n\$sample = ! ! \$a;\n\$sample = ~  \$c;\nfunction & foo(){}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NotOperatorWithSpaceFixer, NotOperatorWithSuccessorSpaceFixer.
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
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            if ($tokensAnalyzer->isUnarySuccessorOperator($index)) {
                if (!$tokens[$tokens->getPrevNonWhitespace($index)]->isComment()) {
                    $tokens->removeLeadingWhitespace($index);
                }
                continue;
            }
            if ($tokensAnalyzer->isUnaryPredecessorOperator($index)) {
                $tokens->removeTrailingWhitespace($index);
                continue;
            }
        }
    }
}
