<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarDumper\Caster;

use Ds\Collection;
use Ds\Map;
use Ds\Pair;
use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub;
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
     * @param \Ds\Collection $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castCollection($c, array $a, $stub, $isNested)
    {
        $a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'count'] = $c->count();
        $a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'capacity'] = $c->capacity();
        if (!$c instanceof \Ds\Map) {
            $a += $c->toArray();
        }
        return $a;
    }
    /**
     * @param \Ds\Map $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castMap($c, array $a, $stub, $isNested)
    {
        foreach ($c as $k => $v) {
            $a[] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\DsPairStub($k, $v);
        }
        return $a;
    }
    /**
     * @param \Ds\Pair $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPair($c, array $a, $stub, $isNested)
    {
        foreach ($c->toArray() as $k => $v) {
            $a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . $k] = $v;
        }
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\DsPairStub $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castPairStub($c, array $a, $stub, $isNested)
    {
        if ($isNested) {
            $stub->class = \Ds\Pair::class;
            $stub->value = null;
            $stub->handle = 0;
            $a = $c->value;
        }
        return $a;
    }
}
