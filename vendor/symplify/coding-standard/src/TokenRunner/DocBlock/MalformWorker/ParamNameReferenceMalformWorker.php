<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20220607\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class ParamNameReferenceMalformWorker implements MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/B4rWNk/3
     */
    private const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\\$\\w+)#';
    /**
     * @param Tokens<Token> $tokens
     */
    public function work(string $docContent, Tokens $tokens, int $position) : string
    {
        return Strings::replace($docContent, self::PARAM_NAME_REGEX, function ($match) : string {
            return $match['param'] . $match['paramName'];
        });
    }
}
