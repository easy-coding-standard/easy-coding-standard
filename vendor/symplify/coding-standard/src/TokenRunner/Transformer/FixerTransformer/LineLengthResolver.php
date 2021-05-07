<?php

namespace Symplify\CodingStandard\TokenRunner\Transformer\FixerTransformer;

use ECSPrefix20210507\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo;
use Symplify\PackageBuilder\Configuration\StaticEolConfiguration;
final class LineLengthResolver
{
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return int
     */
    public function getLengthFromStartEnd($tokens, $blockInfo)
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
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $position
     * @return bool
     */
    private function isNewLineOrOpenTag($tokens, $position)
    {
        /** @var Token $currentToken */
        $currentToken = $tokens[$position];
        if (\ECSPrefix20210507\Nette\Utils\Strings::startsWith($currentToken->getContent(), \Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) {
            return \true;
        }
        return $currentToken->isGivenKind(\T_OPEN_TAG);
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return int
     */
    private function getLengthFromFunctionStartToEndOfArguments($blockInfo, $tokens)
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
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \Symplify\CodingStandard\TokenRunner\ValueObject\BlockInfo $blockInfo
     * @return int
     */
    private function getLengthFromEndOfArgumentToLineBreak($blockInfo, $tokens)
    {
        $length = 0;
        $end = $blockInfo->getEnd();
        /** @var Token $currentToken */
        $currentToken = $tokens[$end];
        while (!\ECSPrefix20210507\Nette\Utils\Strings::startsWith($currentToken->getContent(), \Symplify\PackageBuilder\Configuration\StaticEolConfiguration::getEolChar())) {
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
