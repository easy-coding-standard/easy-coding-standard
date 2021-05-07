<?php

namespace Symplify\CodingStandard\Tokens;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class LineResolver
{
    /**
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return int
     */
    public function resolve($tokens, $position)
    {
        $lineCount = 0;
        for ($i = 0; $i < $position; ++$i) {
            if (!isset($tokens[$i])) {
                break;
            }
            /** @var Token $token */
            $token = $tokens[$i];
            $lineCount += \substr_count($token->getContent(), \PHP_EOL);
        }
        return $lineCount;
    }
}
