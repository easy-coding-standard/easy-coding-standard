<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\ConfigTransformer\DumperFomatter;

use ConfigTransformer20210601\Nette\Utils\Strings;
final class YamlDumpFormatter
{
    /**
     * @see https://regex101.com/r/HrIt7K/1
     * @var string
     */
    const PRE_SPACE_TAG_REGEX = '#- [\\d]+: { (\\w+):#';
    /**
     * @see https://regex101.com/r/xJF0qK/2
     * @var string
     */
    const ANONYMOUS_CLASS_ID_REGEX = '#(\\n    )(\\d+)\\:#m';
    /**
     * @see https://regex101.com/r/jU1GhX/1
     * @var string
     */
    const SERVICES_REGEX = '#services:\\n\\n    (\\w)#m';
    /**
     * @see https://regex101.com/r/xywp1A/1
     * @var string
     */
    const FOUR_SPACES_TO_CONTENT_REGEX = '#\\n    (\\w)#m';
    public function format(string $content) : string
    {
        // not sure why, but sometimes there is extra value in the start
        $content = \ltrim($content);
        $content = $this->addExtraSpaceBetweenServiceDefinitions($content);
        $content = $this->clearExtraTagZeroName($content);
        return $this->replaceAnonymousIdsWithDash($content);
    }
    private function addExtraSpaceBetweenServiceDefinitions(string $content) : string
    {
        // put extra empty line between service definitions, to make them better readable
        $content = \ConfigTransformer20210601\Nette\Utils\Strings::replace($content, self::FOUR_SPACES_TO_CONTENT_REGEX, "\n\n    \$1");
        // except the first line under "services:"
        return \ConfigTransformer20210601\Nette\Utils\Strings::replace($content, self::SERVICES_REGEX, "services:\n    \$1");
    }
    private function replaceAnonymousIdsWithDash(string $content) : string
    {
        return \ConfigTransformer20210601\Nette\Utils\Strings::replace($content, self::ANONYMOUS_CLASS_ID_REGEX, '$1-');
    }
    private function clearExtraTagZeroName(string $content) : string
    {
        // remove pre-space in tags, kinda hacky
        return \ConfigTransformer20210601\Nette\Utils\Strings::replace($content, self::PRE_SPACE_TAG_REGEX, '- { $1:');
    }
}
