<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * Collapses a multi-line @var doc block placed above an inline variable into a single line.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\SingleLineInlineVarDocBlockFixer\SingleLineInlineVarDocBlockFixerTest
 */
final class SingleLineInlineVarDocBlockFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @see https://regex101.com/r/GkyV1C/1
     * @var string
     */
    private const SINGLE_ASTERISK_START_REGEX = '#^/\*\s+\*(\s+@(?:psalm-|phpstan-)?var)#';
    /**
     * @see https://regex101.com/r/9cfhFI/1
     * @var string
     */
    private const SPACE_REGEX = '#\s+#m';
    /**
     * @see https://regex101.com/r/VpTDCd/1
     * @var string
     */
    private const ASTERISK_LEFTOVERS_REGEX = '#(\*\*)(\s+\*)#';
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Collapse a multi-line inline @var doc block into a single line';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        if (!$this->isVariableComment($tokens, $position)) {
            return $docContent;
        }
        // more than 2 newlines - keep it
        if (substr_count($docContent, "\n") > 2) {
            return $docContent;
        }
        // asterisk start
        $docContent = Regex::replace($docContent, self::SINGLE_ASTERISK_START_REGEX, '/**$1');
        // inline
        $docContent = Regex::replace($docContent, self::SPACE_REGEX, ' ');
        // remove asterisk leftover
        return Regex::replace($docContent, self::ASTERISK_LEFTOVERS_REGEX, '$1');
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isVariableComment(Tokens $tokens, int $position): bool
    {
        $nextPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextPosition === null) {
            return \false;
        }
        $nextNextPosition = $tokens->getNextMeaningfulToken($nextPosition + 2);
        if ($nextNextPosition === null) {
            return \false;
        }
        /** @var Token $nextNextToken */
        $nextNextToken = $tokens[$nextNextPosition];
        if ($nextNextToken->isGivenKind([\T_STATIC, \T_FUNCTION])) {
            return \false;
        }
        // is inline variable
        /** @var Token $nextToken */
        $nextToken = $tokens[$nextPosition];
        return $nextToken->isGivenKind(\T_VARIABLE);
    }
}
