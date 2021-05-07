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
 * Casts SPL related classes to array representation.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @final
 */
class SplCaster
{
    const SPL_FILE_OBJECT_FLAGS = [\SplFileObject::DROP_NEW_LINE => 'DROP_NEW_LINE', \SplFileObject::READ_AHEAD => 'READ_AHEAD', \SplFileObject::SKIP_EMPTY => 'SKIP_EMPTY', \SplFileObject::READ_CSV => 'READ_CSV'];
    /**
     * @param \ArrayObject $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castArrayObject($c, array $a, $stub, $isNested)
    {
        return self::castSplArray($c, $a, $stub, $isNested);
    }
    /**
     * @param \ArrayIterator $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castArrayIterator($c, array $a, $stub, $isNested)
    {
        return self::castSplArray($c, $a, $stub, $isNested);
    }
    /**
     * @param \Iterator $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     */
    public static function castHeap($c, array $a, $stub, $isNested)
    {
        $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'heap' => \iterator_to_array(clone $c)];
        return $a;
    }
    /**
     * @param \SplDoublyLinkedList $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castDoublyLinkedList($c, array $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $mode = $c->getIteratorMode();
        $c->setIteratorMode(\SplDoublyLinkedList::IT_MODE_KEEP | $mode & ~\SplDoublyLinkedList::IT_MODE_DELETE);
        $a += [$prefix . 'mode' => new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(($mode & \SplDoublyLinkedList::IT_MODE_LIFO ? 'IT_MODE_LIFO' : 'IT_MODE_FIFO') . ' | ' . ($mode & \SplDoublyLinkedList::IT_MODE_DELETE ? 'IT_MODE_DELETE' : 'IT_MODE_KEEP'), $mode), $prefix . 'dllist' => \iterator_to_array($c)];
        $c->setIteratorMode($mode);
        return $a;
    }
    /**
     * @param \SplFileInfo $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castFileInfo($c, array $a, $stub, $isNested)
    {
        static $map = ['path' => 'getPath', 'filename' => 'getFilename', 'basename' => 'getBasename', 'pathname' => 'getPathname', 'extension' => 'getExtension', 'realPath' => 'getRealPath', 'aTime' => 'getATime', 'mTime' => 'getMTime', 'cTime' => 'getCTime', 'inode' => 'getInode', 'size' => 'getSize', 'perms' => 'getPerms', 'owner' => 'getOwner', 'group' => 'getGroup', 'type' => 'getType', 'writable' => 'isWritable', 'readable' => 'isReadable', 'executable' => 'isExecutable', 'file' => 'isFile', 'dir' => 'isDir', 'link' => 'isLink', 'linkTarget' => 'getLinkTarget'];
        $prefix = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        unset($a["\0SplFileInfo\0fileName"]);
        unset($a["\0SplFileInfo\0pathName"]);
        if (\PHP_VERSION_ID < 80000) {
            if (\false === $c->getPathname()) {
                $a[$prefix . '⚠'] = 'The parent constructor was not called: the object is in an invalid state';
                return $a;
            }
        } else {
            try {
                $c->isReadable();
            } catch (\RuntimeException $e) {
                if ('Object not initialized' !== $e->getMessage()) {
                    throw $e;
                }
                $a[$prefix . '⚠'] = 'The parent constructor was not called: the object is in an invalid state';
                return $a;
            } catch (\Error $e) {
                if ('Object not initialized' !== $e->getMessage()) {
                    throw $e;
                }
                $a[$prefix . '⚠'] = 'The parent constructor was not called: the object is in an invalid state';
                return $a;
            }
        }
        foreach ($map as $key => $accessor) {
            try {
                $a[$prefix . $key] = $c->{$accessor}();
            } catch (\Exception $e) {
            }
        }
        if (isset($a[$prefix . 'realPath'])) {
            $a[$prefix . 'realPath'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\LinkStub($a[$prefix . 'realPath']);
        }
        if (isset($a[$prefix . 'perms'])) {
            $a[$prefix . 'perms'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\sprintf('0%o', $a[$prefix . 'perms']), $a[$prefix . 'perms']);
        }
        static $mapDate = ['aTime', 'mTime', 'cTime'];
        foreach ($mapDate as $key) {
            if (isset($a[$prefix . $key])) {
                $a[$prefix . $key] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\date('Y-m-d H:i:s', $a[$prefix . $key]), $a[$prefix . $key]);
            }
        }
        return $a;
    }
    /**
     * @param \SplFileObject $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castFileObject($c, array $a, $stub, $isNested)
    {
        static $map = ['csvControl' => 'getCsvControl', 'flags' => 'getFlags', 'maxLineLen' => 'getMaxLineLen', 'fstat' => 'fstat', 'eof' => 'eof', 'key' => 'key'];
        $prefix = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        foreach ($map as $key => $accessor) {
            try {
                $a[$prefix . $key] = $c->{$accessor}();
            } catch (\Exception $e) {
            }
        }
        if (isset($a[$prefix . 'flags'])) {
            $flagsArray = [];
            foreach (self::SPL_FILE_OBJECT_FLAGS as $value => $name) {
                if ($a[$prefix . 'flags'] & $value) {
                    $flagsArray[] = $name;
                }
            }
            $a[$prefix . 'flags'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ConstStub(\implode('|', $flagsArray), $a[$prefix . 'flags']);
        }
        if (isset($a[$prefix . 'fstat'])) {
            $a[$prefix . 'fstat'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\CutArrayStub($a[$prefix . 'fstat'], ['dev', 'ino', 'nlink', 'rdev', 'blksize', 'blocks']);
        }
        return $a;
    }
    /**
     * @param \SplObjectStorage $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castObjectStorage($c, array $a, $stub, $isNested)
    {
        $storage = [];
        unset($a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_DYNAMIC . "\0gcdata"]);
        // Don't hit https://bugs.php.net/65967
        unset($a["\0SplObjectStorage\0storage"]);
        $clone = clone $c;
        foreach ($clone as $obj) {
            $storage[] = ['object' => $obj, 'info' => $clone->getInfo()];
        }
        $a += [\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'storage' => $storage];
        return $a;
    }
    /**
     * @param \OuterIterator $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castOuterIterator($c, array $a, $stub, $isNested)
    {
        $a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'innerIterator'] = $c->getInnerIterator();
        return $a;
    }
    /**
     * @param \WeakReference $c
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     */
    public static function castWeakReference($c, array $a, $stub, $isNested)
    {
        $a[\ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL . 'object'] = $c->get();
        return $a;
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\VarDumper\Cloner\Stub $stub
     * @param bool $isNested
     * @return mixed[]
     */
    private static function castSplArray($c, array $a, $stub, $isNested)
    {
        $prefix = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::PREFIX_VIRTUAL;
        $flags = $c->getFlags();
        if (!($flags & \ArrayObject::STD_PROP_LIST)) {
            $c->setFlags(\ArrayObject::STD_PROP_LIST);
            $a = \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\Caster::castObject($c, \get_class($c), \method_exists($c, '__debugInfo'), $stub->class);
            $c->setFlags($flags);
        }
        if (\PHP_VERSION_ID < 70400) {
            $a[$prefix . 'storage'] = $c->getArrayCopy();
        }
        $a += [$prefix . 'flag::STD_PROP_LIST' => (bool) ($flags & \ArrayObject::STD_PROP_LIST), $prefix . 'flag::ARRAY_AS_PROPS' => (bool) ($flags & \ArrayObject::ARRAY_AS_PROPS)];
        if ($c instanceof \ArrayObject) {
            $a[$prefix . 'iteratorClass'] = new \ECSPrefix20210507\Symfony\Component\VarDumper\Caster\ClassStub($c->getIteratorClass());
        }
        return $a;
    }
}
