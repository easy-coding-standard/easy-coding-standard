<?php

namespace Symplify\CodingStandard\Tokens;

use ECSPrefix20210513\Nette\Utils\Strings;
/**
 * Heavily inspired by
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class CommentedLineTrimmer
{
    /**
     * @var string[]
     */
    const OPENING_LINE = ['//', '#'];
    /**
     * @param string $tokenContent
     * @return string
     */
    public function trim($tokenContent)
    {
        $tokenContent = (string) $tokenContent;
        foreach (self::OPENING_LINE as $openingLine) {
            if (!\ECSPrefix20210513\Nette\Utils\Strings::startsWith($tokenContent, $openingLine)) {
                continue;
            }
            return \substr($tokenContent, \strlen($openingLine));
        }
        return $tokenContent;
    }
}
