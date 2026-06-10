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
 * Removes dead @param annotation lines that only contain a variable name and no type,
 * e.g. "@param $value" - such line carries no information and can be removed.
 *
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\RemoveDeadParamFixer\RemoveDeadParamFixerTest
 */
final class RemoveDeadParamFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Remove a dead @param line that has only a name and no type';
    /**
     * @see https://regex101.com/r/Hk4lFc/1
     * @var string
     */
    private const PARAM_WITHOUT_TYPE_REGEX = '#@(?:psalm-|phpstan-)?param\s+\$\w+\s*$#';
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
            if (!Regex::match($line->getContent(), self::PARAM_WITHOUT_TYPE_REGEX)) {
                continue;
            }
            $line->remove();
        }
        return $docBlock->getContent();
    }
}
