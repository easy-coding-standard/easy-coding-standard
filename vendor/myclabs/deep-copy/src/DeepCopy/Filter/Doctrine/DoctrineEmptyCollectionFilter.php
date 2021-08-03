<?php

namespace ECSPrefix20210803\DeepCopy\Filter\Doctrine;

use ECSPrefix20210803\DeepCopy\Filter\Filter;
use ECSPrefix20210803\DeepCopy\Reflection\ReflectionHelper;
use ECSPrefix20210803\Doctrine\Common\Collections\ArrayCollection;
/**
 * @final
 */
class DoctrineEmptyCollectionFilter implements \ECSPrefix20210803\DeepCopy\Filter\Filter
{
    /**
     * Sets the object property to an empty doctrine collection.
     *
     * @param object   $object
     * @param string   $property
     * @param callable $objectCopier
     */
    public function apply($object, $property, $objectCopier)
    {
        $reflectionProperty = \ECSPrefix20210803\DeepCopy\Reflection\ReflectionHelper::getProperty($object, $property);
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, new \ECSPrefix20210803\Doctrine\Common\Collections\ArrayCollection());
    }
}
