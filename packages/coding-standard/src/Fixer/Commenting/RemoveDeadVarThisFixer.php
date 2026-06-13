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
 * Removes a dead inline var doc block that types the "$this" variable - it carries no
 * information, as the type of "$this" is already known from the surrounding class.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveDeadVarThisFixer\RemoveDeadVarThisFixerTest
 */
final class RemoveDeadVarThisFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove a dead inline "@var ... $this" doc block';
    /**
     * @see https://regex101.com/r/Hk4lFc/2
     * @var string
     */
    private const VAR_THIS_REGEX = '#@(?:psalm-|phpstan-)?var\b[^\n]*\$this\b#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        $docBlock = new DocBlock($docContent);
        foreach ($docBlock->getLines() as $line) {
            if (!Regex::match($line->getContent(), self::VAR_THIS_REGEX)) {
                continue;
            }
            $line->remove();
        }
        return $docBlock->getContent();
    }
}
