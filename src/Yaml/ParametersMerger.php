<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Yaml;

final class ParametersMerger
{
    /**
     * Merges configurations. Left has higher priority than right one.
     *
     * @autor David Grudl (https://davidgrudl.com)
     * @source https://github.com/nette/di/blob/8eb90721a131262f17663e50aee0032a62d0ef08/src/DI/Config/Helpers.php#L31
     *
     * @param mixed $left
     * @param mixed $right
     * @return mixed[]|string
     */
    public function merge($left, $right)
    {
        if (is_array($left) && is_array($right)) {
            foreach ($left as $key => $val) {
                if (is_int($key)) {
                    $right[] = $val;
                } else {
                    if (isset($right[$key])) {
                        $val = $this->merge($val, $right[$key]);
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
