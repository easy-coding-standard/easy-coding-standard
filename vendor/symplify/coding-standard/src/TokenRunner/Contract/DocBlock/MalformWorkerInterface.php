<?php

namespace Symplify\CodingStandard\TokenRunner\Contract\DocBlock;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
interface MalformWorkerInterface
{
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     */
    public function work($docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string;
}
