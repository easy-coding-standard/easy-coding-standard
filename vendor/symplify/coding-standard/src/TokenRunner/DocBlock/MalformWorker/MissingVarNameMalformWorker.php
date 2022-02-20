<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\DocBlock\MalformWorker;

use ECSPrefix20220220\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface;
final class MissingVarNameMalformWorker implements \Symplify\CodingStandard\TokenRunner\Contract\DocBlock\MalformWorkerInterface
{
    /**
     * @var string
     * @see https://regex101.com/r/s1UkZs/1
     */
    private const VAR_WITHOUT_NAME_REGEX = '#^(?<open>\\/\\*\\* @(?:psalm-|phpstan-)?var )(?<type>[\\\\\\w\\|-|]+)(?<close>\\s+\\*\\/)$#';
    /**
     * @param Tokens<Token> $tokens
     */
    public function work(string $docContent, \PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : string
    {
        if (!\ECSPrefix20220220\Nette\Utils\Strings::match($docContent, self::VAR_WITHOUT_NAME_REGEX)) {
            return $docContent;
        }
        $nextVariableToken = $this->getNextVariableToken($tokens, $position);
        if (!$nextVariableToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return $docContent;
        }
        return \ECSPrefix20220220\Nette\Utils\Strings::replace($docContent, self::VAR_WITHOUT_NAME_REGEX, function (array $match) use($nextVariableToken) : string {
            return $match['open'] . $match['type'] . ' ' . $nextVariableToken->getContent() . $match['close'];
        });
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function getNextVariableToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : ?\PhpCsFixer\Tokenizer\Token
    {
        $nextMeaningfulTokenPosition = $tokens->getNextMeaningfulToken($position);
        if ($nextMeaningfulTokenPosition === null) {
            return null;
        }
        $nextToken = $tokens[$nextMeaningfulTokenPosition] ?? null;
        if (!$nextToken instanceof \PhpCsFixer\Tokenizer\Token) {
            return null;
        }
        if (!$nextToken->isGivenKind(\T_VARIABLE)) {
            return null;
        }
        return $nextToken;
    }
}
