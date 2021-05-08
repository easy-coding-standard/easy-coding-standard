<?php

namespace Symplify\CodingStandard\DocBlock;

use ECSPrefix20210508\Nette\Utils\Strings;
use Symplify\CodingStandard\Tokens\CommentedLineTrimmer;
/**
 * Heavily inspired by
 *
 * @see https://github.com/squizlabs/PHP_CodeSniffer/blob/master/src/Standards/Squiz/Sniffs/PHP/CommentedOutCodeSniff.php
 */
final class Decommenter
{
    /**
     * @see https://regex101.com/r/MbNMeH/1
     * @var string
     */
    const LINE_BREAKER_REGEX = '#[\\-=\\#\\*]{2,}#';
    /**
     * @var CommentedLineTrimmer
     */
    private $commentedLineTrimmer;
    public function __construct(\Symplify\CodingStandard\Tokens\CommentedLineTrimmer $commentedLineTrimmer)
    {
        $this->commentedLineTrimmer = $commentedLineTrimmer;
    }
    /**
     * @param string $content
     */
    public function decoment($content) : string
    {
        if (\is_object($content)) {
            $content = (string) $content;
        }
        $lines = \explode(\PHP_EOL, $content);
        foreach ($lines as $key => $line) {
            $lines[$key] = $this->commentedLineTrimmer->trim($line);
        }
        $uncommentedContent = \implode(\PHP_EOL, $lines);
        $uncommentedContent = \ltrim($uncommentedContent);
        return $this->clearContent($uncommentedContent);
    }
    /**
     * Quite a few comments use multiple dashes, equals signs etc to frame comments and licence headers.
     * @param string $content
     */
    private function clearContent($content) : string
    {
        if (\is_object($content)) {
            $content = (string) $content;
        }
        return \ECSPrefix20210508\Nette\Utils\Strings::replace($content, self::LINE_BREAKER_REGEX, '-');
    }
}
