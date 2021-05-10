<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\VarDumper\Caster;

use ECSPrefix20210510\Doctrine\Common\Proxy\Proxy as CommonProxy;
use ECSPrefix20210510\Doctrine\ORM\PersistentCollection;
use ECSPrefix20210510\Doctrine\ORM\Proxy\Proxy as OrmProxy;
use ECSPrefix20210510\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * Casts Doctrine related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class DoctrineCaster
{
    /**
     * @param bool $isNested
     */
    public static function castCommonProxy(\ECSPrefix20210510\Doctrine\Common\Proxy\Proxy $proxy, array $a, \ECSPrefix20210510\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach (['__cloner__', '__initializer__'] as $k) {
            if (\array_key_exists($k, $a)) {
                unset($a[$k]);
                ++$stub->cut;
            }
        }
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castOrmProxy(\ECSPrefix20210510\Doctrine\ORM\Proxy\Proxy $proxy, array $a, \ECSPrefix20210510\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach (['_entityPersister', '_identifier'] as $k) {
            if (\array_key_exists($k = "\0Doctrine\\ORM\\Proxy\\Proxy\0" . $k, $a)) {
                unset($a[$k]);
                ++$stub->cut;
            }
        }
        return $a;
    }
    /**
     * @param bool $isNested
     */
    public static function castPersistentCollection(\ECSPrefix20210510\Doctrine\ORM\PersistentCollection $coll, array $a, \ECSPrefix20210510\Symfony\Component\VarDumper\Cloner\Stub $stub, $isNested)
    {
        $isNested = (bool) $isNested;
        foreach (['snapshot', 'association', 'typeClass'] as $k) {
            if (\array_key_exists($k = "\0Doctrine\\ORM\\PersistentCollection\0" . $k, $a)) {
                $a[$k] = new \ECSPrefix20210510\Symfony\Component\VarDumper\Caster\CutStub($a[$k]);
            }
        }
        return $a;
    }
}
