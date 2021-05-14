<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210514\Symfony\Component\VarDumper\Caster;

use Ds\Collection;
use Ds\Map;
use Ds\Pair;
use ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\Stub;
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
    public static function castCollection(\Ds\Collection $c, array $a, \ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $a[\ECSPrefix20210514\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'count'] = $c->count();
        $a[\ECSPrefix20210514\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'capacity'] = $c->capacity();
        if (!$c instanceof \Ds\Map) {
            $a += $c->toArray();
        }
        return $a;
    }
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castMap(\Ds\Map $c, array $a, \ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach ($c as $k => $v) {
            $a[] = new \ECSPrefix20210514\Symfony\Component\VarDumper\Caster\DsPairStub($k, $v);
        }
        return $a;
    }
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPair(\Ds\Pair $c, array $a, \ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach ($c->toArray() as $k => $v) {
            $a[\ECSPrefix20210514\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . $k] = $v;
        }
        return $a;
    }
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPairStub(\ECSPrefix20210514\Symfony\Component\VarDumper\Caster\DsPairStub $c, array $a, \ECSPrefix20210514\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        if ($isNested) {
            $stub->class = \Ds\Pair::class;
            $stub->value = null;
            $stub->handle = 0;
            $a = $c->value;
        }
        return $a;
    }
}
