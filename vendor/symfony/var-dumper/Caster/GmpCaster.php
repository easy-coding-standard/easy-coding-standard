<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210508\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts GMP objects to array representation.
 *
 * @author Hamza Amrouche <hamza.simperfit@gmail.com>
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class GmpCaster
{
    /**
     * @param bool $isNested
     * @param int $filter
     * @return mixed[]
     */
    public static function castGmp(\GMP $gmp, array $a, \ECSPrefix20210508\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested, $filter)
    {
        $a[\ECSPrefix20210508\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'value'] = new \ECSPrefix20210508\Symfony\Component\VarDumper\Caster\ConstStub(\gmp_strval($gmp), \gmp_strval($gmp));
        return $a;
    }
}
