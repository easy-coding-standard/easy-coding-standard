<?php

namespace Symplify\CodingStandard\ValueObject;

use ECSPrefix20210512\Nette\Utils\Strings;
final class DocBlockLines
{
    /**
     * @var array<string>
     */
    private $descriptionLines = [];
    /**
     * @var array<string>
     */
    private $otherLines = [];
    /**
     * @param array<string> $descriptionLines
     * @param array<string> $otherLines
     */
    public function __construct(array $descriptionLines, array $otherLines)
    {
        $this->descriptionLines = $descriptionLines;
        $this->otherLines = $otherLines;
    }
    /**
     * @return mixed[]
     */
    public function getDescriptionLines()
    {
        return $this->descriptionLines;
    }
    /**
     * @return mixed[]
     */
    public function getOtherLines()
    {
        return $this->otherLines;
    }
    /**
     * @return bool
     */
    public function hasListDescriptionLines()
    {
        foreach ($this->descriptionLines as $descriptionLine) {
            if (\ECSPrefix20210512\Nette\Utils\Strings::startsWith($descriptionLine, '-')) {
                return \true;
            }
        }
        return \false;
    }
}
