<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\Php;

use PhpToken;
use Symplify\CodingStandard\TokenRunner\TokenFinder;
use Symplify\CodingStandard\Tokens\CommentedContentResolver;
final class PhpContentAnalyzer
{
    /**
     * @var int[]
     */
    const STMT_OPENING_TYPES = [\T_IF, \T_WHILE, \T_DO, \T_FOR, \T_SWITCH];
    /**
     * @var int[]
     */
    const END_SEMI_COLON_TYPES = [\T_INCLUDE, \T_EMPTY, \T_USE];
    /**
     * @var \Symplify\CodingStandard\TokenRunner\TokenFinder
     */
    private $tokenFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\TokenFinder $tokenFinder)
    {
        $this->tokenFinder = $tokenFinder;
    }
    public function isPhpContent(string $content) : bool
    {
        // is content commented PHP code?
        $rawTokens = $this->parseCodeToTokens($content);
        $tokenCount = \count($rawTokens);
        // probably not content
        if ($tokenCount < 3) {
            return \false;
        }
        // has 2 strings after each other, not PHP code
        if ($this->hasTwoStringsTokensInRow($tokenCount, $rawTokens)) {
            return \false;
        }
        $firstInLineLintedCorrectly = \false;
        for ($i = 0; $i < $tokenCount; ++$i) {
            $rawToken = $rawTokens[$i];
            // twig
            if ($rawToken->text === '{') {
                $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                if (!$nextToken instanceof \PhpToken) {
                    return \false;
                }
                if ($nextToken->text === '%') {
                    return \false;
                }
            }
            if (!$firstInLineLintedCorrectly) {
                if ($rawToken->is(\T_CONSTANT_ENCAPSED_STRING)) {
                    return \false;
                }
                if ($rawToken->is(self::END_SEMI_COLON_TYPES)) {
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if (!$lastLineToken instanceof \PhpToken) {
                        return \false;
                    }
                    if ($lastLineToken->text !== ';') {
                        return \false;
                    }
                }
                if ($rawToken->is(\T_DEFAULT)) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (!$nextToken instanceof \PhpToken) {
                        return \false;
                    }
                    if ($nextToken->text !== ':') {
                        return \false;
                    }
                }
                // token id: 311
                if ($rawToken->is(\T_STRING)) {
                    return \false;
                }
                if ($rawToken->is(\T_NAMESPACE)) {
                    // is namespace part
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (!$nextToken instanceof \PhpToken) {
                        return \false;
                    }
                }
                if ($rawToken->is(\T_VARIABLE)) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (!$nextToken instanceof \PhpToken) {
                        return \false;
                    }
                    if ($nextToken->is(\T_STRING)) {
                        return \false;
                    }
                }
                if ($rawToken->is(self::STMT_OPENING_TYPES)) {
                    // has expected end?
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if (!$lastLineToken instanceof \PhpToken) {
                        return \true;
                    }
                    return \in_array($lastLineToken->text, ['{', ')'], \true);
                }
                // these cannot be in the start of line
                if ($rawToken->is([\T_LOGICAL_AND, \T_LOGICAL_OR, \T_LOGICAL_XOR])) {
                    return \false;
                }
            }
            if ($rawToken->is(\T_FUNCTION) && !$this->isFunctionStart($rawTokens, $i)) {
                return \false;
            }
            if (!$firstInLineLintedCorrectly) {
                $firstInLineLintedCorrectly = \true;
            }
            // is comment content
            if ($rawToken->is(\Symplify\CodingStandard\Tokens\CommentedContentResolver::EMPTY_TOKENS)) {
                continue;
            }
            // new line comming next → restart string check
            if ($rawToken->text === \PHP_EOL) {
                $firstInLineLintedCorrectly = \false;
            }
        }
        return \true;
    }
    /**
     * @param PhpToken[] $tokens
     */
    private function isFunctionStart(array $tokens, int $i) : bool
    {
        $twoNextTokens = $this->tokenFinder->getNextMeaninfulTokens($tokens, $i + 1, 2);
        if (\count($twoNextTokens) !== 2) {
            return \false;
        }
        $nameToken = $twoNextTokens[0];
        $openBracketToken = $twoNextTokens[1];
        if (!$nameToken->is(\T_STRING)) {
            return \false;
        }
        return $openBracketToken->text === '(';
    }
    /**
     * @param PhpToken[] $rawTokens
     */
    private function hasTwoStringsTokensInRow(int $tokenCount, array $rawTokens) : bool
    {
        for ($i = 0; $i < $tokenCount; ++$i) {
            $token = $rawTokens[$i];
            if (!$token->is(\T_STRING)) {
                continue;
            }
            $nextTokens = $this->tokenFinder->getNextMeaninfulTokens($rawTokens, $i + 1, 1);
            if ($nextTokens === []) {
                continue;
            }
            if (!$nextTokens[0]->is(\T_STRING)) {
                continue;
            }
            return \true;
        }
        return \false;
    }
    /**
     * @return PhpToken[]
     */
    private function parseCodeToTokens(string $content) : array
    {
        $phpContent = '<?php ' . \PHP_EOL . $content;
        $rawTokens = \PhpToken::tokenize($phpContent);
        return \array_slice($rawTokens, 2);
    }
}
