<?php

declare (strict_types=1);
namespace ECSPrefix20210801\Symplify\AutowireArrayParameter\DocBlock;

use ECSPrefix20210801\Nette\Utils\Strings;
/**
 * @see \Symplify\AutowireArrayParameter\Tests\DocBlock\ParamTypeDocBlockResolverTest
 */
final class ParamTypeDocBlockResolver
{
    /**
     * @var string
     */
    const TYPE_PART = 'type';
    /**
     * Copied mostly from
     * https://github.com/nette/di/blob/d1c0598fdecef6d3b01e2ace5f2c30214b3108e6/src/DI/Autowiring.php#L215
     *
     * @see https://regex101.com/r/wGteeZ/1
     * @var string
     */
    const NORMAL_REGEX = '#@param\\s+(?<' . self::TYPE_PART . '>[\\w\\\\]+)\\[\\]\\s+\\$' . self::NAME_PLACEHOLDER . '#';
    /**
     * @var string
     * @see https://regex101.com/r/FZ50hn/2
     */
    const SHAPE_REGEX = '#@param\\s+(array|iterable)\\<(?<' . self::TYPE_PART . '>[\\w\\\\]+)\\>\\s+\\$' . self::NAME_PLACEHOLDER . '#';
    /**
     * @var string
     */
    const NAME_PLACEHOLDER = '__NAME__';
    /**
     * @var string[]
     */
    const ARRAY_REGEXES = [self::NORMAL_REGEX, self::SHAPE_REGEX];
    /**
     * @return string|null
     */
    public function resolve(string $docBlock, string $parameterName)
    {
        foreach (self::ARRAY_REGEXES as $arrayRegexWithPlaceholder) {
            $arrayRegex = \str_replace(self::NAME_PLACEHOLDER, $parameterName, $arrayRegexWithPlaceholder);
            $result = \ECSPrefix20210801\Nette\Utils\Strings::match($docBlock, $arrayRegex);
            if (isset($result[self::TYPE_PART])) {
                return $result[self::TYPE_PART];
            }
        }
        return null;
    }
}
