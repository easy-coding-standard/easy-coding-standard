<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\ValueObjectFactory;

use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException;
use Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition;
use ECSPrefix20220220\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class LineLengthAndPositionFactory
{
    /**
     * @param Tokens<Token> $tokens
     */
    public function createFromTokensAndLineStartPosition(\PhpCsFixer\Tokenizer\Tokens $tokens, int $currentPosition) : \Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition
    {
        $length = 0;
        while (!$this->isNewLineOrOpenTag($tokens, $currentPosition)) {
            // in case of multiline string, we are interested in length of the part on current line only
            if (!isset($tokens[$currentPosition])) {
                throw new \Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException($currentPosition);
            }
            $explode = \explode("\n", $tokens[$currentPosition]->getContent());
            // string precedes current token, so we are interested in end part only
            if ($explode !== []) {
                $lastSection = \end($explode);
                $length += \strlen($lastSection);
            }
            --$currentPosition;
            if (\count($explode) > 1) {
                // no longer need to continue searching for newline
                break;
            }
            if (!isset($tokens[$currentPosition])) {
                break;
            }
        }
        return new \Symplify\CodingStandard\TokenRunner\ValueObject\LineLengthAndPosition($length, $currentPosition);
    }
    /**
     * @param Tokens<Token> $tokens
     */
    private function isNewLineOrOpenTag(\PhpCsFixer\Tokenizer\Tokens $tokens, int $position) : bool
    {
        if (!isset($tokens[$position])) {
            throw new \Symplify\CodingStandard\TokenRunner\Exception\TokenNotFoundException($position);
        }
        if (\strncmp($tokens[$position]->getContent(), \ECSPrefix20220220\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar(), \strlen(\ECSPrefix20220220\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) === 0) {
            return \true;
        }
        return $tokens[$position]->isGivenKind(\T_OPEN_TAG);
    }
}
