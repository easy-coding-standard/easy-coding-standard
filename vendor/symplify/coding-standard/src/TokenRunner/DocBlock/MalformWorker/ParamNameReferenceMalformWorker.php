<?php

namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20210508\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class ParamNameReferenceMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/B4rWNk/3
     */
    const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\\$\\w+)#';
    /**
     * @param Tokens<Token> $tokens
     * @param string $docContent
     */
    public function work($docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        if (\is_object($docContent)) {
            $docContent = (string) $docContent;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($docContent, self::PARAM_NAME_REGEX, function ($match) : string {
            return $match['param'] . $match['paramName'];
        });
    }
}
