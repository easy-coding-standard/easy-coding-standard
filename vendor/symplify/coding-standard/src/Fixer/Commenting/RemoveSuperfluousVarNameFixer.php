<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveSuperfluousVarNameFixer\RemoveSuperfluousVarNameFixerTest
 */
final class RemoveSuperfluousVarNameFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove a superfluous variable name from a property @var annotation';
    /**
     * @see https://regex101.com/r/euhrn8/1
     * @var string
     */
    private const THIS_VARIABLE_REGEX = '#\$this$#';
    /**
     * @see https://regex101.com/r/6XuSGV/1
     * @var string
     */
    private const VAR_VARIABLE_NAME_REGEX = '#(?<tag>@(?:psalm-|phpstan-)?var)(?<type>\s+[|\\\\\\w]+)?(\s+)(?<propertyName>\$[\w]+)#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        if ($this->shouldSkip($tokens, $position)) {
            return $docContent;
        }
        $docBlock = new DocBlock($docContent);
        $lines = $docBlock->getLines();
        foreach ($lines as $line) {
            $match = Regex::match($line->getContent(), self::VAR_VARIABLE_NAME_REGEX);
            if ($match === null) {
                continue;
            }
            $newLineContent = Regex::replace($line->getContent(), self::VAR_VARIABLE_NAME_REGEX, static function (array $match): string {
                $replacement = $match['tag'];
                if ($match['type'] !== []) {
                    $replacement .= $match['type'];
                }
                if (Regex::match($match['propertyName'], self::THIS_VARIABLE_REGEX)) {
                    return $match['tag'] . ' self';
                }
                return $replacement;
            });
            $line->setContent($newLineContent);
        }
        return $docBlock->getContent();
    }
    /**
     * Is property doc block?
     *
     * @param Tokens<Token> $tokens
     */
    private function shouldSkip(Tokens $tokens, int $position): bool
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);
        // nothing to change
        if ($nextMeaningfulTokenPosition === null) {
            return \true;
        }
        /** @var Token $nextMeaningfulToken */
        $nextMeaningfulToken = $tokens[$nextMeaningfulTokenPosition];
        // should be protected/private/public/static, to know we're property
        return !$nextMeaningfulToken->isGivenKind([\T_PUBLIC, \T_PROTECTED, \T_PRIVATE, \T_STATIC]);
    }
}
