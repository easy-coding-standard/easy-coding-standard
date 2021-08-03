<?php

namespace ECSPrefix20210803\DeepCopy;

use function function_exists;
if (\false === \function_exists('ECSPrefix20210803\\DeepCopy\\deep_copy')) {
    /**
     * Deep copies the given value.
     *
     * @param mixed $value
     * @param bool  $useCloneMethod
     *
     * @return mixed
     */
    function deep_copy($value, $useCloneMethod = \false)
    {
        return (new \ECSPrefix20210803\DeepCopy\DeepCopy($useCloneMethod))->copy($value);
    }
}
