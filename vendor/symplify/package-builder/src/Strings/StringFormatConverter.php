<?php

namespace Symplify\PackageBuilder\Strings;

use Nette\Utils\Strings;

/**
 * @api
 * @see \Symplify\PackageBuilder\Tests\Strings\StringFormatConverterTest
 */
final class StringFormatConverter
{
    /**
     * @var string
     * @see https://regex101.com/r/rl1nvl/1
     */
    const BIG_LETTER_REGEX = '#([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]*)#';

    /**
     * @param string $value
     * @return string
     */
    public function underscoreAndHyphenToCamelCase($value)
    {
        $value = (string) $value;
        $underscoreToHyphensValue = str_replace(['_', '-'], ' ', $value);
        $uppercasedWords = ucwords($underscoreToHyphensValue);
        $value = str_replace(' ', '', $uppercasedWords);

        return lcfirst($value);
    }

    /**
     * @param string $input
     * @return string
     */
    public function camelCaseToUnderscore($input)
    {
        $input = (string) $input;
        return $this->camelCaseToGlue($input, '_');
    }

    /**
     * @param string $input
     * @return string
     */
    public function camelCaseToDashed($input)
    {
        $input = (string) $input;
        return $this->camelCaseToGlue($input, '-');
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function camelCaseToUnderscoreInArrayKeys(array $items)
    {
        foreach ($items as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $newKey = $this->camelCaseToUnderscore($key);
            if ($key === $newKey) {
                continue;
            }

            $items[$newKey] = $value;
            unset($items[$key]);
        }

        return $items;
    }

    /**
     * @param string $input
     * @param string $glue
     * @return string
     */
    private function camelCaseToGlue($input, $glue)
    {
        $input = (string) $input;
        $glue = (string) $glue;
        $matches = Strings::matchAll($input, self::BIG_LETTER_REGEX);

        $parts = [];
        foreach ($matches as $match) {
            $parts[] = $match[0] === strtoupper($match[0]) ? strtolower($match[0]) : lcfirst($match[0]);
        }

        return implode($glue, $parts);
    }
}
