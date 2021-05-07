<?php

namespace Symplify\PackageBuilder\Php;

final class TypeChecker
{
    /**
     * @param array<class-string> $types
     * @return bool
     */
    public function isInstanceOf($object, array $types)
    {
        foreach ($types as $type) {
            if (\is_a($object, $type, \true)) {
                return \true;
            }
        }
        return \false;
    }
}
