<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeUnit;

use function array_merge;
use function count;
use Countable;
use IteratorAggregate;
final class CodeUnitCollection implements \Countable, \IteratorAggregate
{
    /**
     * @psalm-var list<CodeUnit>
     */
    private $codeUnits = [];
    /**
     * @psalm-param list<CodeUnit> $items
     * @return $this
     * @param mixed[] $items
     */
    public static function fromArray($items)
    {
        $collection = new self();
        foreach ($items as $item) {
            $collection->add($item);
        }
        return $collection;
    }
    /**
     * @return $this
     * @param \SebastianBergmann\CodeUnit\CodeUnit ...$items
     */
    public static function fromList(...$items)
    {
        return self::fromArray($items);
    }
    private function __construct()
    {
    }
    /**
     * @psalm-return list<CodeUnit>
     */
    public function asArray() : array
    {
        return $this->codeUnits;
    }
    public function getIterator() : \ECSPrefix20210804\SebastianBergmann\CodeUnit\CodeUnitCollectionIterator
    {
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\CodeUnitCollectionIterator($this);
    }
    public function count() : int
    {
        return \count($this->codeUnits);
    }
    public function isEmpty() : bool
    {
        return empty($this->codeUnits);
    }
    /**
     * @param $this $other
     * @return $this
     */
    public function mergeWith($other)
    {
        return self::fromArray(\array_merge($this->asArray(), $other->asArray()));
    }
    /**
     * @return void
     */
    private function add(\ECSPrefix20210804\SebastianBergmann\CodeUnit\CodeUnit $item)
    {
        $this->codeUnits[] = $item;
    }
}
