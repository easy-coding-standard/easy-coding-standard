<?php

namespace Symplify\CodingStandard\ValueObjectFactory;

use ECSPrefix20210508\Nette\Utils\Strings;
use Symplify\CodingStandard\ValueObject\DocBlockLines;
final class DocBlockLinesFactory
{
    /**
     * @see https://regex101.com/r/CUxOj5/1
     * @var string
     */
    const BEGINNING_OF_DOC_BLOCK_REGEX = '/^(\\/\\*\\*[\\n]?)/';
    /**
     * @see https://regex101.com/r/otQGPe/1
     * @var string
     */
    const END_OF_DOC_BLOCK_REGEX = '/(\\*\\/)$/';
    /**
     * @param string $docBlock
     * @return \Symplify\CodingStandard\ValueObject\DocBlockLines
     */
    public function createFromDocBlock($docBlock)
    {
        $docBlock = (string) $docBlock;
        // Remove the prefix '/**'
        $docBlock = \ECSPrefix20210508\Nette\Utils\Strings::replace($docBlock, self::BEGINNING_OF_DOC_BLOCK_REGEX);
        // Remove the suffix '*/'
        $docBlock = \ECSPrefix20210508\Nette\Utils\Strings::replace($docBlock, self::END_OF_DOC_BLOCK_REGEX);
        // Remove extra whitespace at the end
        $docBlock = \rtrim($docBlock);
        $docBlockLines = $this->splitToLines($docBlock);
        $docBlockLines = \array_map(function (string $line) : string {
            $noWhitespace = \ECSPrefix20210508\Nette\Utils\Strings::trim($line, \ECSPrefix20210508\Nette\Utils\Strings::TRIM_CHARACTERS);
            // Remove asterisks on the left side, plus additional whitespace
            return \ltrim($noWhitespace, \ECSPrefix20210508\Nette\Utils\Strings::TRIM_CHARACTERS . '*');
        }, $docBlockLines);
        return $this->createFromLines($docBlockLines);
    }
    /**
     * @param string[] $docBlockLines
     * @return \Symplify\CodingStandard\ValueObject\DocBlockLines
     */
    private function createFromLines(array $docBlockLines)
    {
        $descriptionLines = [];
        $otherLines = [];
        $collectDescriptionLines = \true;
        foreach ($docBlockLines as $docBlockLine) {
            if (\ECSPrefix20210508\Nette\Utils\Strings::startsWith($docBlockLine, '@') || \ECSPrefix20210508\Nette\Utils\Strings::startsWith($docBlockLine, '{@')) {
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
     * @return mixed[]
     * @param string $string
     */
    private function splitToLines($string)
    {
        $string = (string) $string;
        return \explode(\PHP_EOL, $string);
    }
}
