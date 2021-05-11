<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210511\Ramsey\Uuid\UuidInterface;
use ECSPrefix20210511\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class UuidCaster
{
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castRamseyUuid(\ECSPrefix20210511\Ramsey\Uuid\UuidInterface $c, array $a, \ECSPrefix20210511\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $a += [\ECSPrefix20210511\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'uuid' => (string) $c];
        return $a;
    }
}
