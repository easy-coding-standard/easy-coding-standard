<?php

namespace Symplify\CodingStandard\Php;

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
     * @var TokenFinder
     */
    private $tokenFinder;
    public function __construct(\Symplify\CodingStandard\TokenRunner\TokenFinder $tokenFinder)
    {
        $this->tokenFinder = $tokenFinder;
    }
    /**
     * @param string $content
     */
    public function isPhpContent($content) : bool
    {
        if (\is_object($content)) {
            $content = (string) $content;
        }
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
            if ($rawToken === '{') {
                $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                if ($nextToken === '%') {
                    return \false;
                }
            }
            if (!isset($rawToken[2])) {
                continue;
            }
            if (!$firstInLineLintedCorrectly) {
                $tokenKind = $rawToken[0];
                if ($tokenKind === \T_CONSTANT_ENCAPSED_STRING) {
                    return \false;
                }
                if (\in_array($tokenKind, self::END_SEMI_COLON_TYPES, \true)) {
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if ($lastLineToken !== ';') {
                        return \false;
                    }
                }
                if ($tokenKind === \T_DEFAULT) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (!\is_array($nextToken)) {
                        return \false;
                    }
                    if ($nextToken !== ':') {
                        return \false;
                    }
                }
                // token id: 311
                if ($tokenKind === \T_STRING) {
                    return \false;
                }
                if ($tokenKind === \T_NAMESPACE) {
                    // is namespace part
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if (!\is_array($nextToken)) {
                        return \false;
                    }
                }
                if ($tokenKind === \T_VARIABLE) {
                    $nextToken = $this->tokenFinder->getNextMeaninfulToken($rawTokens, $i + 1);
                    if ($nextToken === []) {
                        return \false;
                    }
                    if (!\is_array($nextToken)) {
                        return \false;
                    }
                    if ($nextToken[0] === \T_STRING) {
                        return \false;
                    }
                }
                if (\in_array($tokenKind, self::STMT_OPENING_TYPES, \true)) {
                    // has expected end?
                    $lastLineToken = $this->tokenFinder->getSameRowLastToken($rawTokens, $i + 1);
                    if (!\is_array($lastLineToken)) {
                        return \true;
                    }
                    return \in_array($lastLineToken[0], ['{', ')'], \true);
                }
                // these cannot be in the start of line
                if (\in_array($tokenKind, [\T_LOGICAL_AND, \T_LOGICAL_OR, \T_LOGICAL_XOR], \true)) {
                    return \false;
                }
            }
            if ($rawToken[0] === \T_FUNCTION && !$this->isFunctionStart($rawTokens, $i)) {
                return \false;
            }
            if (!$firstInLineLintedCorrectly) {
                $firstInLineLintedCorrectly = \true;
            }
            // is comment content
            if (\in_array($rawToken[0], \Symplify\CodingStandard\Tokens\CommentedContentResolver::EMPTY_TOKENS, \true)) {
                continue;
            }
            // new line comming next → restart string check
            if ($rawToken[1] === \PHP_EOL) {
                $firstInLineLintedCorrectly = \false;
            }
        }
        return \true;
    }
    /**
     * @param mixed[] $tokens
     * @param int $i
     * @return bool
     */
    private function isFunctionStart(array $tokens, $i)
    {
        $twoNextTokens = $this->tokenFinder->getNextMeaninfulTokens($tokens, $i + 1, 2);
        if (\count($twoNextTokens) !== 2) {
            return \false;
        }
        $nameToken = $twoNextTokens[0];
        $openBracketToken = $twoNextTokens[1];
        if ($nameToken[0] !== \T_STRING) {
            return \false;
        }
        return $openBracketToken === '(';
    }
    /**
     * @param int $tokenCount
     * @return bool
     */
    private function hasTwoStringsTokensInRow($tokenCount, array $rawTokens)
    {
        for ($i = 0; $i < $tokenCount; ++$i) {
            $token = $rawTokens[$i];
            if ($token[0] !== \T_STRING) {
                continue;
            }
            $nextTokens = $this->tokenFinder->getNextMeaninfulTokens($rawTokens, $i + 1, 1);
            if ($nextTokens === []) {
                continue;
            }
            if ($nextTokens[0][0] !== \T_STRING) {
                continue;
            }
            return \true;
        }
        return \false;
    }
    /**
     * @noRector
     * @return string[][]|int[][]|string[]
     */
    private function parseCodeToTokens(string $content) : array
    {
        $phpContent = '<?php ' . \PHP_EOL . $content;
        $rawTokens = \token_get_all($phpContent);
        return \array_slice($rawTokens, 2);
    }
}
