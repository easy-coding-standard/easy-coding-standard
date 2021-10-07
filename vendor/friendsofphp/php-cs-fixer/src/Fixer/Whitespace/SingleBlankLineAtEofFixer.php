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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * A file must always end with a line endings character.
 *
 * Fixer for rules defined in PSR2 ¶2.2.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class SingleBlankLineAtEofFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('A PHP file without end tag must always end with a single empty line feed.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = 1;"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = 1;\n\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        // must run last to be sure the file is properly formatted before it runs
        return -50;
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
        $count = $tokens->count();
        if ($count > 0 && !$tokens[$count - 1]->isGivenKind([\T_INLINE_HTML, \T_CLOSE_TAG, \T_OPEN_TAG])) {
            $tokens->ensureWhitespaceAtIndex($count - 1, 1, $this->whitespacesConfig->getLineEnding());
        }
    }
}
