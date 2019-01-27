<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Utils;

final class StringConverter
{
    public function underscoreToCamelCase(string $value): string
    {
        $value = str_replace(' ', '', ucwords(str_replace('_', ' ', $value)));

        $value[0] = strtolower($value[0]);

        return $value;
    }

    public function camelCaseToUnderscore(string $value): string
    {
        $underscoredVariable = '';
        $length = strlen($value);

        for ($i = 0; $i < $length; $i++) {
            if (ctype_upper($value[$i])) {
                $underscoredVariable .= '_';
            }
            $underscoredVariable .= $value[$i];
        }

        return strtolower($underscoredVariable);
    }

    /**
     * @param mixed[] $items
     * @return mixed[]
     */
    public function camelCaseToUnderscoreInArrayKeys(array $items): array
    {
        foreach ($items as $key => $value) {
            $newKey = $this->camelCaseToUnderscore($key);
            if ($key === $newKey) {
                continue;
            }

            $items[$newKey] = $value;
            unset($items[$key]);
        }

        return $items;
    }
}
