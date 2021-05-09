<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;

final class ParamNameReferenceMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/B4rWNk/3
     */
    const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\$\w+)#';

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
        return Strings::replace($docContent, self::PARAM_NAME_REGEX, function ($match): string {
            return $match['param'] . $match['paramName'];
        });
    }
}
