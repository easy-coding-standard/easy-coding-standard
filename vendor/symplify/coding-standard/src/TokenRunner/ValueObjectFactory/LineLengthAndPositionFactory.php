<?php

namespace Symplify\CodingStandard\TokenRunner\ValueObjectFactory;

use Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;

final class LineLengthAndPositionFactory
{
    /**
     * @param Tokens<Token> $tokens
     * @param int $currentPosition
     * @return \Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition
     */
    public function createFromTokensAndLineStartPosition(Tokens $tokens, $currentPosition)
    {
        $currentPosition = (int) $currentPosition;
        $length = 0;

        while (! $this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            // in case of multiline string, we are interested in length of the part on current line only
            if (! isset($tokens[$currentPosition])) {
                throw new TokenNotFoundException($currentPosition);
            }

            $explode = explode("\n", $tokens[$currentPosition]->getContent());
            // string precedes current token, so we are interested in end part only
            if (count($explode) !== 0) {
                $lastSection = end($explode);
                $length += strlen($lastSection);
            }

            --$currentPosition;

            if (count($explode) > 1) {
                // no longer need to continue searching for newline
                break;
            }

            if (! isset($tokens[$currentPosition])) {
                break;
            }
        }

        return new LineLengthAndPosition($length, $currentPosition);
    }

    /**
     * @param Tokens|Token[] $tokens
     * @param int $position
     * @return bool
     */
    private function isNewLineOrOpenTag(Tokens $tokens, $position)
    {
        $position = (int) $position;
        if (! isset($tokens[$position])) {
            throw new TokenNotFoundException($position);
        }

        if (Strings::startsWith($tokens[$position]->getContent(), StaticEolConfiguration::getEolChar())) {
            return true;
        }

        return $tokens[$position]->isGivenKind(T_OPEN_TAG);
    }
}
