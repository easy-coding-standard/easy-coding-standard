<?php

namespace ECSPrefix20210803\DeepCopy\TypeFilter\Spl;

use Closure;
use ECSPrefix20210803\DeepCopy\DeepCopy;
use ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter;
use SplDoublyLinkedList;
/**
 * @final
 */
class SplDoublyLinkedListFilter implements \ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter
{
    private $copier;
    public function __construct(\ECSPrefix20210803\DeepCopy\DeepCopy $copier)
    {
        $this->copier = $copier;
    }
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        $newElement = clone $element;
        $copy = $this->createCopyClosure();
        return $copy($newElement);
    }
    private function createCopyClosure()
    {
        $copier = $this->copier;
        $copy = function (\SplDoublyLinkedList $list) use($copier) {
            // Replace each element in the list with a deep copy of itself
            for ($i = 1; $i <= $list->count(); $i++) {
                $copy = $copier->recursiveCopy($list->shift());
                $list->push($copy);
            }
            return $list;
        };
        return \Closure::bind($copy, null, \ECSPrefix20210803\DeepCopy\DeepCopy::class);
    }
}
