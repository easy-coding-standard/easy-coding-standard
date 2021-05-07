<?php

namespace Symplify\PackageBuilder\Matcher;

final class ArrayStringAndFnMatcher
{
    /**
     * @param string[] $matchingValues
     * @param string $currentValue
     * @return bool
     */
    public function isMatchWithIsA($currentValue, array $matchingValues)
    {
        if ($this->isMatch($currentValue, $matchingValues)) {
            return \true;
        }
        foreach ($matchingValues as $matchingValue) {
            if (\is_a($currentValue, $matchingValue, \true)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param string[] $matchingValues
     * @param string $currentValue
     * @return bool
     */
    public function isMatch($currentValue, array $matchingValues)
    {
        foreach ($matchingValues as $matchingValue) {
            if ($currentValue === $matchingValue) {
                return \true;
            }
            if (\fnmatch($matchingValue, $currentValue)) {
                return \true;
            }
            if (\fnmatch($matchingValue, $currentValue, \FNM_NOESCAPE)) {
                return \true;
            }
        }
        return \false;
    }
}
