<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\Contract\DocBlock;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
interface MalformWorkerInterface
{
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     * @param int $position
     */
    public function work($docContent, $tokens, $position) : string;
}
