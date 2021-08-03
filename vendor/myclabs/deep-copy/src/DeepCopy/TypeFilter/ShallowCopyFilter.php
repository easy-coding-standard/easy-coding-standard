<?php

namespace ECSPrefix20210803\DeepCopy\TypeFilter;

/**
 * @final
 */
class ShallowCopyFilter implements \ECSPrefix20210803\DeepCopy\TypeFilter\TypeFilter
{
    /**
     * {@inheritdoc}
     */
    public function apply($element)
    {
        return clone $element;
    }
}
