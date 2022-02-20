<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\ValueObjectFactory;

use ECSPrefix20220220\Nette\Utils\Strings;
use Symplify\CodingStandard\ValueObject\DocBlockLines;
final class DocBlockLinesFactory
{
    /**
     * @see https://regex101.com/r/CUxOj5/1
     * @var string
     */
    private const BEGINNING_OF_DOC_BLOCK_REGEX = '/^(\\/\\*\\*[\\n]?)/';
    /**
     * @see https://regex101.com/r/otQGPe/1
     * @var string
     */
    private const END_OF_DOC_BLOCK_REGEX = '/(\\*\\/)$/';
    public function createFromDocBlock(string $docBlock) : \Symplify\CodingStandard\ValueObject\DocBlockLines
    {
        // Remove the prefix '/**'
        $docBlock = \ECSPrefix20220220\Nette\Utils\Strings::replace($docBlock, self::BEGINNING_OF_DOC_BLOCK_REGEX);
        // Remove the suffix '*/'
        $docBlock = \ECSPrefix20220220\Nette\Utils\Strings::replace($docBlock, self::END_OF_DOC_BLOCK_REGEX);
        // Remove extra whitespace at the end
        $docBlock = \rtrim($docBlock);
        $docBlockLines = $this->splitToLines($docBlock);
        $docBlockLines = \array_map(function (string $line) : string {
            $noWhitespace = \ECSPrefix20220220\Nette\Utils\Strings::trim($line, \ECSPrefix20220220\Nette\Utils\Strings::TRIM_CHARACTERS);
            // Remove asterisks on the left side, plus additional whitespace
            return \ltrim($noWhitespace, \ECSPrefix20220220\Nette\Utils\Strings::TRIM_CHARACTERS . '*');
        }, $docBlockLines);
        return $this->createFromLines($docBlockLines);
    }
    /**
     * @param string[] $docBlockLines
     */
    private function createFromLines(array $docBlockLines) : \Symplify\CodingStandard\ValueObject\DocBlockLines
    {
        $descriptionLines = [];
        $otherLines = [];
        $collectDescriptionLines = \true;
        foreach ($docBlockLines as $docBlockLine) {
            if (\strncmp($docBlockLine, '@', \strlen('@')) === 0 || \strncmp($docBlockLine, '{@', \strlen('{@')) === 0) {
                // The line has a special meaning (it's an annotation, or something like {@inheritdoc})
                $collectDescriptionLines = \false;
            }
            if ($collectDescriptionLines) {
                $descriptionLines[] = $docBlockLine;
            } else {
                $otherLines[] = $docBlockLine;
            }
        }
        return new \Symplify\CodingStandard\ValueObject\DocBlockLines($descriptionLines, $otherLines);
    }
    /**
     * @return string[]
     */
    private function splitToLines(string $string) : array
    {
        return \explode(\PHP_EOL, $string);
    }
}
