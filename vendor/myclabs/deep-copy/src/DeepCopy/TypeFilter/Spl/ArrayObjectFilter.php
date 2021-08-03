<?php

namespace ECSPrefix20210803\DeepCopy\TypeFilter\Spl;

use ECSPrefix20210803\DeepCopy\DeepCopy;
use ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter;
/**
 * In PHP 7.4 the storage of an ArrayObject isn't returned as
 * ReflectionProperty. So we deep copy its array copy.
 */
final class ArrayObjectFilter implements \ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter
{
    /**
     * @var DeepCopy
     */
    private $copier;
    public function __construct(\ECSPrefix20210803\DeepCopy\DeepCopy $copier)
    {
        $this->copier = $copier;
    }
    /**
     * {@inheritdoc}
     */
    public function apply($arrayObject)
    {
        $clone = clone $arrayObject;
        foreach ($arrayObject->getArrayCopy() as $k => $v) {
            $clone->offsetSet($k, $this->copier->copy($v));
        }
        return $clone;
    }
}
