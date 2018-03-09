<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class ArrayMerger
{
    /**
     * @param mixed[] $arrays
     * @return mixed[]
     */
    public static function mergeRecursively(array $arrays): array
    {
        $result = [];
        foreach ($arrays as $array) {
            $result = self::merge($result, $array);
        }

        return $result;
    }

    /**
     * With modifications
     *
     * @param mixed $left
     * @param mixed $right
     * @return mixed
     *
     * @author David Grudl
     * @source https://github.com/nette/di/blob/401dca5375c2bc991bff59e632c89270efc75542/src/DI/Config/Helpers.php#L31-L56
     */
    public static function merge($left, $right)
    {
        if (is_array($left) && is_array($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (isset($right[$key])) {
                        $val = static::merge($val, $right[$key]);
                    }

                    $right[$key] = $val;
                }
            }

            return $right;
        } elseif ($left === null && is_array($right)) {
            return $right;
        }

        return $left;
    }
}
