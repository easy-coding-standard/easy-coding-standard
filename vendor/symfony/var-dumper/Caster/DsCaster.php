<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\VarDumper\Caster;

use Ds\Collection;
use Ds\Map;
use Ds\Pair;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * Casts Ds extension classes to array representation.
 *
 * @author Jáchym Toušek <enumag@gmail.com>
 *
 * @final
 */
class DsCaster
{
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castCollection(Collection $c, array $a, Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $a[Caster::PREFIX_VIRTUAL.'count'] = $c->count();
        $a[Caster::PREFIX_VIRTUAL.'capacity'] = $c->capacity();

        if (!$c instanceof Map) {
            $a += $c->toArray();
        }

        return $a;
    }

    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castMap(Map $c, array $a, Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach ($c as $k => $v) {
            $a[] = new DsPairStub($k, $v);
        }

        return $a;
    }

    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPair(Pair $c, array $a, Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach ($c->toArray() as $k => $v) {
            $a[Caster::PREFIX_VIRTUAL.$k] = $v;
        }

        return $a;
    }

    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPairStub(DsPairStub $c, array $a, Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        if ($isNested) {
            $stub->class = Pair::class;
            $stub->value = null;
            $stub->handle = 0;

            $a = $c->value;
        }

        return $a;
    }
}
