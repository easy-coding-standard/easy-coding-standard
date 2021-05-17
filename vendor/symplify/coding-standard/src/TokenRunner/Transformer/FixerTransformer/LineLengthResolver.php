<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use ECSPrefix20210517\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use ECSPrefix20210517\Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class LineLengthResolver
{
    /**
     * @param Tokens|Token[] $tokens
     * @return int
     */
    public function getLengthFromStartEnd(\PhpCsFixer\Tokenizer\Tokens $tokens, \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo)
    {
        $lineLength = 0;
        // compute from function to start of line
        $start = $blockInfo->getStart();
        while (!$this->isNewLineOrOpenTag($tokens, $start)) {
            $lineLength += \strlen($tokens[$start]->getContent());
            --$start;
            if (!isset($tokens[$start])) {
                break;
            }
        }
        // get spaces to first line
        $lineLength += \strlen($tokens[$start]->getContent());
        // get length from start of function till end of arguments - with spaces as one
        $lineLength += $this->getLengthFromFunctionStartToEndOfArguments($blockInfo, $tokens);
        // get length from end or arguments to first line break
        $lineLength += $this->getLengthFromEndOfArgumentToLineBreak($blockInfo, $tokens);
        return $lineLength;
    }
    /**
     * @param Tokens|Token[] $tokens
     * @param int $position
     * @return bool
     */
    private function isNewLineOrOpenTag(\PhpCsFixer\Tokenizer\Tokens $tokens, $position)
    {
        $position = (int) $position;
        /** @var Token $currentToken */
        $currentToken = $tokens[$position];
        if (\ECSPrefix20210517\Nette\Utils\Strings::startsWith($currentToken->getContent(), \ECSPrefix20210517\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) {
            return \true;
        }
        return $currentToken->isGivenKind(\T_OPEN_TAG);
    }
    /**
     * @param Tokens|Token[] $tokens
     * @return int
     */
    private function getLengthFromFunctionStartToEndOfArguments(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $length = 0;
        $start = $blockInfo->getStart();
        while ($start < $blockInfo->getEnd()) {
            /** @var Token $currentToken */
            $currentToken = $tokens[$start];
            if ($currentToken->isGivenKind(\T_WHITESPACE)) {
                ++$length;
                ++$start;
                continue;
            }
            $length += \strlen($currentToken->getContent());
            ++$start;
            if (!isset($tokens[$start])) {
                break;
            }
        }
        return $length;
    }
    /**
     * @param Tokens|Token[] $tokens
     * @return int
     */
    private function getLengthFromEndOfArgumentToLineBreak(\Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $length = 0;
        $end = $blockInfo->getEnd();
        /** @var Token $currentToken */
        $currentToken = $tokens[$end];
        while (!\ECSPrefix20210517\Nette\Utils\Strings::startsWith($currentToken->getContent(), \ECSPrefix20210517\Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) {
            $length += \strlen($currentToken->getContent());
            ++$end;
            if (!isset($tokens[$end])) {
                break;
            }
            /** @var Token $currentToken */
            $currentToken = $tokens[$end];
        }
        return $length;
    }
}
