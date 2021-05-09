<?php

namespace Symplify\CodingStandard\Tokens;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\ValueObject\StartAndEnd;
use Symplify\SymplifyKernel\Exception\ShouldNotHappenException;

/**
 * Heavily inspired by
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class CommentedContentResolver
{
    /**
     * @var int[]
     */
    const EMPTY_TOKENS = [T_WHITESPACE, T_STRING, T_ENCAPSED_AND_WHITESPACE, T_COMMENT];

    /**
     * @var LineResolver
     */
    private $lineResolver;

    public function __construct(LineResolver $lineResolver)
    {
        $this->lineResolver = $lineResolver;
    }

    /**
     * @param Tokens<Token> $tokens
     * @param int $position
     * @return \Symplify\CodingStandard\ValueObject\StartAndEnd
     */
    public function resolve(Tokens $tokens, $position)
    {
        $position = (int) $position;
        $token = $tokens[$position];
        if (! $token->isGivenKind(T_COMMENT)) {
            throw new ShouldNotHappenException();
        }

        $lastLineSeen = $this->lineResolver->resolve($tokens, $position);
        $startPosition = $position;
        $lastPosition = $position;

        // content after token
        for ($i = $position; $i < $tokens->count(); ++$i) {
            /** @var Token $token */
            $token = $tokens[$i];

            if (! $token->isGivenKind(self::EMPTY_TOKENS)) {
                continue;
            }

            if ($token->isGivenKind(T_WHITESPACE)) {
                continue;
            }

            $tokenLine = $this->lineResolver->resolve($tokens, $i);

            if ($this->shouldBreak($lastLineSeen, $tokenLine, $token)) {
                break;
            }

            $lastPosition = $i;
            $lastLineSeen = $tokenLine;

            // Trim as much off the comment as possible so we don't, have additional whitespace tokens or comment tokens
            $tokenContent = trim($token->getContent());
            $hasBlockCommentCloser = Strings::endsWith($tokenContent, '*/');

            if ($hasBlockCommentCloser) {
                // Closer of a block comment found
                break;
            }
        }

        return new StartAndEnd($startPosition, $lastPosition);
    }

    /**
     * @param int $lastLineSeen
     * @param int $tokenLine
     * @return bool
     */
    private function shouldBreak($lastLineSeen, $tokenLine, Token $token)
    {
        $lastLineSeen = (int) $lastLineSeen;
        $tokenLine = (int) $tokenLine;
        if ($lastLineSeen + 1 <= $tokenLine
            && Strings::startsWith($token->getContent(), '/*')
        ) {
            // First non-whitespace token on a new line is start of a different style comment.
            return true;
        }

        if ($this->isNextLineNotComment($lastLineSeen, $tokenLine, $token)) {
            return true;
        }

        // Blank line breaks a '//' style comment block.
        return $lastLineSeen + 1 < $tokenLine;
    }

    /**
     * @param int $lastLineSeen
     * @param int $tokenLine
     * @return bool
     */
    private function isNextLineNotComment($lastLineSeen, $tokenLine, Token $token)
    {
        $lastLineSeen = (int) $lastLineSeen;
        $tokenLine = (int) $tokenLine;
        if ($lastLineSeen >= $tokenLine) {
            return false;
        }

        return ! Strings::startsWith($token->getContent(), '//');
    }
}
