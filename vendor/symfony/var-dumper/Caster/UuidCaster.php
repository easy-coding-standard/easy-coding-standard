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

use Ramsey\Uuid\UuidInterface;
use Symfony\Component\VarDumper\Cloner\Stub;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 */
final class UuidCaster
{
    /**
     * @param bool $isNested
     * @return mixed[]
     */
    public static function castRamseyUuid(UuidInterface $c, array $a, Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        $a += [
            Caster::PREFIX_VIRTUAL.'uuid' => (string) $c,
        ];

        return $a;
    }
}
