<?php

namespace Symplify\PackageBuilder\Neon;

use ECSPrefix20210507\Nette\Neon\Encoder;
use ECSPrefix20210507\Nette\Neon\Neon;
use ECSPrefix20210507\Nette\Utils\Strings;
final class NeonPrinter
{
    /**
     * @see https://regex101.com/r/r8DGyV/1
     * @var string
     */
    const TAGS_REGEX = '#tags:\\s+\\-\\s+(?<tag>.*?)$#ms';
    /**
     * @see https://regex101.com/r/KjekIe/1
     * @var string
     */
    const ARGUMENTS_DOUBLE_SPACE_REGEX = '#\\n(\\n\\s+arguments:)#ms';
    /**
     * @param mixed[] $phpStanNeon
     * @return string
     */
    public function printNeon(array $phpStanNeon)
    {
        $neonContent = \ECSPrefix20210507\Nette\Neon\Neon::encode($phpStanNeon, \ECSPrefix20210507\Nette\Neon\Encoder::BLOCK);
        // tabs to spaces for consistency
        $neonContent = $this->replaceTabsWithSpaces($neonContent);
        // inline single tags, dummy
        $neonContent = $this->inlineSingleTags($neonContent);
        $neonContent = $this->fixDoubleSpaceInArguments($neonContent);
        return \rtrim($neonContent) . \PHP_EOL;
    }
    /**
     * @param string $neonContent
     * @return string
     */
    private function replaceTabsWithSpaces($neonContent)
    {
        return \ECSPrefix20210507\Nette\Utils\Strings::replace($neonContent, '#\\t#', '    ');
    }
    /**
     * @param string $neonContent
     * @return string
     */
    private function inlineSingleTags($neonContent)
    {
        return \ECSPrefix20210507\Nette\Utils\Strings::replace($neonContent, self::TAGS_REGEX, 'tags: [$1]');
    }
    /**
     * @param string $neonContent
     * @return string
     */
    private function fixDoubleSpaceInArguments($neonContent)
    {
        return \ECSPrefix20210507\Nette\Utils\Strings::replace($neonContent, self::ARGUMENTS_DOUBLE_SPACE_REGEX, '$1');
    }
}
