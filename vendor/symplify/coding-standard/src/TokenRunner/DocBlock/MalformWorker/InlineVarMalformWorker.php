<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class InlineVarMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/8OuO60/1
     */
    const SINGLE_ASTERISK_START_REGEX = '#^/\*(\n?\s+@var)#';

    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     * @param int $position
     * @return string
     */
    public function work($docContent, Tokens $tokens, $position)
    {
        $docContent = (string) $docContent;
        $position = (int) $position;
        /** @var Token $token */
        $token = $tokens[$position];

        if (! $token->isGivenKind(T_COMMENT)) {
            return $docContent;
        }

        return Strings::replace($docContent, self::SINGLE_ASTERISK_START_REGEX, '/**$1');
    }
}
