<?php

namespace ECSPrefix20210804\DeepCopy\Filter\Doctrine;

use ECSPrefix20210804\DeepCopy\Filter\Filter;
/**
 * @final
 */
class DoctrineProxyFilter implements \ECSPrefix20210804\DeepCopy\Filter\Filter
{
    /**
     * Triggers the magic method __load() on a Doctrine Proxy class to load the
     * actual entity from the database.
     *
     * {@inheritdoc}
     */
    public function apply($object, $property, $objectCopier)
    {
        $object->__load();
    }
}
