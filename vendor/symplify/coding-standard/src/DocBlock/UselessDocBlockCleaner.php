<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\DocBlock;

use ECSPrefix202503\Nette\Utils\Strings;
use PhpCsFixer\Tokenizer\Token;
final class UselessDocBlockCleaner
{
    /**
     * @var string[]
     */
    private const CLEANING_REGEXES = [
        self::TODO_COMMENT_BY_PHPSTORM_REGEX,
        self::TODO_IMPLEMENT_METHOD_COMMENT_BY_PHPSTORM_REGEX,
        self::COMMENT_CONSTRUCTOR_CLASS_REGEX,
        // must run first
        self::STANDALONE_DOCBLOCK_CLASS_REGEX,
        // then this one
        self::STANDALONE_COMMENT_CLASS_REGEX,
        // then this one
        self::INLINE_COMMENT_CLASS_REGEX,
    ];
    /**
     * @see https://regex101.com/r/5fQJkz/2
     * @var string
     */
    private const TODO_IMPLEMENT_METHOD_COMMENT_BY_PHPSTORM_REGEX = '#\\/\\/ TODO: Implement .*\\(\\) method.$#';
    /**
     * @see https://regex101.com/r/zayQpv/1
     * @var string
     */
    private const TODO_COMMENT_BY_PHPSTORM_REGEX = '#\\/\\/ TODO: Change the autogenerated stub$#';
    /**
     * @see https://regex101.com/r/RzTdFH/4
     * @var string
     */
    private const STANDALONE_DOCBLOCK_CLASS_REGEX = '#(\\/\\*\\*\\s+)\\*\\s+[cC]lass\\s+[^\\s]*(\\s+\\*\\/)$#';
    /**
     * @see https://regex101.com/r/RzTdFH/4
     * @var string
     */
    private const STANDALONE_COMMENT_CLASS_REGEX = '#\\/\\/\\s+(class|trait|interface)\\s+\\w+$#i';
    /**
     * @see https://regex101.com/r/RzTdFH/4
     * @var string
     */
    private const INLINE_COMMENT_CLASS_REGEX = '#\\s\\*\\s(class|trait|interface)\\s+(\\w)+$#i';
    /**
     * @var string
     */
    private const COMMENT_CONSTRUCTOR_CLASS_REGEX = '#^(\\/\\/|(\\s|\\*)+)(\\s\\w+\\s)?constructor(\\.)?$#i';
    /**
     * @see https://regex101.com/r/1kcgR5/1
     * @var string
     */
    private const DOCTRINE_GENERATED_COMMENT_REGEX = '#^(\\/\\*{2}\\s+?)?(\\*|\\/\\/)\\s+This class was generated by the Doctrine ORM\\. Add your own custom\\r?\\n\\s+\\*\\s+repository methods below\\.(\\s+\\*\\/)$#';
    public function clearDocTokenContent(Token $currentToken, ?string $classLikeName) : string
    {
        $docContent = $currentToken->getContent();
        $cleanedCommentLines = [];
        foreach (\explode("\n", $docContent) as $key => $commentLine) {
            if ($this->isClassLikeName($commentLine, $classLikeName)) {
                continue;
            }
            foreach (self::CLEANING_REGEXES as $cleaningRegex) {
                $commentLine = Strings::replace($commentLine, $cleaningRegex);
            }
            $cleanedCommentLines[$key] = $commentLine;
        }
        // remove empty lines
        $cleanedCommentLines = \array_filter($cleanedCommentLines);
        $cleanedCommentLines = \array_values($cleanedCommentLines);
        // is totally empty?
        if ($this->isEmptyDocblock($cleanedCommentLines)) {
            return '';
        }
        $commentText = \implode("\n", $cleanedCommentLines);
        // run multilines regex on final result
        return Strings::replace($commentText, self::DOCTRINE_GENERATED_COMMENT_REGEX);
    }
    /**
     * @param string[] $commentLines
     */
    private function isEmptyDocblock(array $commentLines) : bool
    {
        if (\count($commentLines) !== 2) {
            return \false;
        }
        $startCommentLine = $commentLines[0];
        $endCommentLine = $commentLines[1];
        return $startCommentLine === '/**' && \trim($endCommentLine) === '*/';
    }
    private function isClassLikeName(string $commentLine, ?string $classLikeName) : bool
    {
        if ($classLikeName === null) {
            return \false;
        }
        return \trim($commentLine, '* ') === $classLikeName;
    }
}
