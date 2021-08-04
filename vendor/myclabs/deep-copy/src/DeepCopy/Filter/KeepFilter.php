<?php

namespace ECSPrefix20210804\DeepCopy\Filter;

class KeepFilter implements \ECSPrefix20210804\DeepCopy\Filter\Filter
{
    /**
     * Keeps the value of the object property.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        // Nothing to do
    }
}
