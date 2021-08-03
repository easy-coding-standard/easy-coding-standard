<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration;

use function count;
use Countable;
use IteratorAggregate;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class DirectoryCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var Directory[]
     */
    private $directories;
    /**
     * @param Directory[] $directories
     */
    public static function fromArray(array $directories) : self
    {
        return new self(...$directories);
    }
    private function __construct(\ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Directory ...$directories)
    {
        $this->directories = $directories;
    }
    /**
     * @return Directory[]
     */
    public function asArray() : array
    {
        return $this->directories;
    }
    public function count() : int
    {
        return \count($this->directories);
    }
    public function getIterator() : \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\DirectoryCollectionIterator
    {
        return new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\DirectoryCollectionIterator($this);
    }
    public function isEmpty() : bool
    {
        return $this->count() === 0;
    }
}
