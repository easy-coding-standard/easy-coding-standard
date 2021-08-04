<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/complexity.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\Complexity;

use Iterator;
final class ComplexityCollectionIterator implements \Iterator
{
    /**
     * @psalm-var list<Complexity>
     */
    private $items;
    /**
     * @var int
     */
    private $position = 0;
    public function __construct(\ECSPrefix20210804\SebastianBergmann\Complexity\ComplexityCollection $items)
    {
        $this->items = $items->asArray();
    }
    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }
    public function valid() : bool
    {
        return isset($this->items[$this->position]);
    }
    public function key() : int
    {
        return $this->position;
    }
    public function current() : \ECSPrefix20210804\SebastianBergmann\Complexity\Complexity
    {
        return $this->items[$this->position];
    }
    /**
     * @return void
     */
    public function next()
    {
        $this->position++;
    }
}
