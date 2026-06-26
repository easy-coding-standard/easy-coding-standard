<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * Fixes a plural-"s" typo on the @return, @param and @var doc block tags (e.g. a stray "@return" + "s" becomes "@return").
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\FixTagTypoFixer\FixTagTypoFixerTest
 */
final class FixTagTypoFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @see https://regex101.com/r/8tFqJp/1
     * @var string
     */
    private const PLURAL_TAG_REGEX = '#@((?:psalm-|phpstan-)?(?:param|return|var))s\b#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition('Fix a plural typo in a doc block tag, e.g. "@returns" to "@return"', []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        return Regex::replace($docContent, self::PLURAL_TAG_REGEX, '@$1');
    }
}
