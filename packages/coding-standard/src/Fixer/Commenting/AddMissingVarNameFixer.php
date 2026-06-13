<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Fixer\Commenting;

use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\Utils\Regex;
/**
 * @see \Symplify\CodingStandard\Tests\Fixer\Commenting\AddMissingVarNameFixer\AddMissingVarNameFixerTest
 */
final class AddMissingVarNameFixer extends \Symplify\CodingStandard\Fixer\Commenting\AbstractDocBlockFixer
{
    /**
     * @var string
     */
    private const ERROR_MESSAGE = 'Add a missing variable name to an inline @var annotation';
    /**
     * @see https://regex101.com/r/s1UkZs/1
     * @var string
     */
    private const VAR_WITHOUT_NAME_REGEX = '#^(?<open>\/\*\* @(?:psalm-|phpstan-)?var )(?<type>[\\\\\\w\|\[\]-]+)(?<close>\s+\*\/)$#';
    public function getDefinition(): FixerDefinitionInterface
    {
        return new FixerDefinition(self::ERROR_MESSAGE, []);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    protected function processDocContent(string $docContent, Tokens $tokens, int $position): string
    {
        if (!Regex::match($docContent, self::VAR_WITHOUT_NAME_REGEX)) {
            return $docContent;
        }
        $nextVariableToken = $this->getNextVariableToken($tokens, $position);
        if (!$nextVariableToken instanceof Token) {
            return $docContent;
        }
        return Regex::replace($docContent, self::VAR_WITHOUT_NAME_REGEX, static function (array $match) use ($nextVariableToken): string {
            return $match['open'] . $match['type'] . ' ' . $nextVariableToken->getContent() . $match['close'];
        });
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextVariableToken(Tokens $tokens, int $position): ?Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        $nextToken = $tokens[$nextMeaningfulTokenPosition] ?? null;
        if (!$nextToken instanceof Token) {
            return null;
        }
        if (!$nextToken->isGivenKind(\T_VARIABLE)) {
            return null;
        }
        return $nextToken;
    }
}
