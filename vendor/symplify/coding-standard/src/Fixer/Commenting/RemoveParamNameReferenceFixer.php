<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveParamNameReferenceFixer\RemoveParamNameReferenceFixerTest
 */
final class RemoveParamNameReferenceFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove the reference "&" from a @param variable name';
    /**
     * @see https://regex101.com/r/B4rWNk/3
     * @var string
     */
    private const PARAM_NAME_REGEX = '#(?<param>@param(.*?))&(?<paramName>\$\w+)#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        return Regex::replace($docContent, self::PARAM_NAME_REGEX, static function ($match): string {
            return $match['param'] . $match['paramName'];
        });
    }
}
