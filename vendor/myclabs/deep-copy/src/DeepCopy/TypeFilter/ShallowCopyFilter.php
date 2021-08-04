<?php

namespace ECSPrefix20210804\DeepCopy\TypeFilter;

/**
 * @final
 */
class ShallowCopyFilter implements \ECSPrefix20210804\DeepCopy\TypeFilter\TypeFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        return clone $element;
    }
}
