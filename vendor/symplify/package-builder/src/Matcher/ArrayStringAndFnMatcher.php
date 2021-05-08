<?php

namespace Symplify\PackageBuilder\Matcher;

final class ArrayStringAndFnMatcher
{
    /**
     * @param string[] $matchingValues
     * @param string $currentValue
     */
    public function isMatchWithIsA($currentValue, array $matchingValues) : bool
    {
        if (\is_object($currentValue)) {
            $currentValue = (string) $currentValue;
        }
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
     */
    public function isMatch($currentValue, array $matchingValues) : bool
    {
        if (\is_object($currentValue)) {
            $currentValue = (string) $currentValue;
        }
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
