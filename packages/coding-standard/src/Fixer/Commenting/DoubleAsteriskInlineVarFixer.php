<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * Turns a single-asterisk inline "/* @var ..." comment into a proper "/** @var ..." doc block.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\DoubleAsteriskInlineVarFixer\DoubleAsteriskInlineVarFixerTest
 */
final class DoubleAsteriskInlineVarFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @see https://regex101.com/r/cj95e6/1
     * @var string
     */
    private const SINGLE_ASTERISK_START_REGEX = '#^/\*(\n?\s+@(?:psalm-|phpstan-)?var)#';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Use a double asterisk "/**" doc block for an inline @var comment';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        /** @var Token $token */
        $token = $tokens[$position];
        if (!$token->isGivenKind(\T_COMMENT)) {
            return $docContent;
        }
        return Regex::replace($docContent, self::SINGLE_ASTERISK_START_REGEX, '/**$1');
    }
}
