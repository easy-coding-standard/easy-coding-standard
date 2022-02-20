<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20220220\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class ParamNameReferenceMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/B4rWNk/3
     */
    private const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\\$\\w+)#';
    /**
     * @param Tokens<Token> $tokens
     */
    public function work(string $docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($docContent, self::PARAM_NAME_REGEX, function ($match) : string {
            return $match['param'] . $match['paramName'];
        });
    }
}
