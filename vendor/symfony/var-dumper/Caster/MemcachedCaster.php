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

use ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub;
/**
 * @author Jan Schädlich <jan.schaedlich@sensiolabs.de>
 *
 * @final
 */
class MemcachedCaster
{
    private static $optionConstants;
    private static $defaultOptions;
    /**
     * @param \Memcached $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castMemcached($c, array $a, $stub, $isNested)
    {
        $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'servers' => $c->getServerList(), \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'options' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\EnumStub(self::getNonDefaultOptions($c))];
        return $a;
    }
    /**
     * @param \Memcached $c
     * @return mixed[]
     */
    private static function getNonDefaultOptions($c)
    {
        self::$defaultOptions = isset(self::$defaultOptions) ? self::$defaultOptions : self::discoverDefaultOptions();
        self::$optionConstants = isset(self::$optionConstants) ? self::$optionConstants : self::getOptionConstants();
        $nonDefaultOptions = [];
        foreach (self::$optionConstants as $constantKey => $value) {
            if (self::$defaultOptions[$constantKey] !== ($option = $c->getOption($value))) {
                $nonDefaultOptions[$constantKey] = $option;
            }
        }
        return $nonDefaultOptions;
    }
    /**
     * @return mixed[]
     */
    private static function discoverDefaultOptions()
    {
        $defaultMemcached = new \Memcached();
        $defaultMemcached->addServer('127.0.0.1', 11211);
        $defaultOptions = [];
        self::$optionConstants = isset(self::$optionConstants) ? self::$optionConstants : self::getOptionConstants();
        foreach (self::$optionConstants as $constantKey => $value) {
            $defaultOptions[$constantKey] = $defaultMemcached->getOption($value);
        }
        return $defaultOptions;
    }
    /**
     * @return mixed[]
     */
    private static function getOptionConstants()
    {
        $reflectedMemcached = new \ReflectionClass(\Memcached::class);
        $optionConstants = [];
        foreach ($reflectedMemcached->getConstants() as $constantKey => $value) {
            if (0 === \strpos($constantKey, 'OPT_')) {
                $optionConstants[$constantKey] = $value;
            }
        }
        return $optionConstants;
    }
}
