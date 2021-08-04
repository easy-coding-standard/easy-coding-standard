<?php

namespace ECSPrefix20210804\DeepCopy\Matcher\Doctrine;

use ECSPrefix20210804\DeepCopy\Matcher\Matcher;
use ECSPrefix20210804\Doctrine\Common\Persistence\Proxy;
/**
 * @final
 */
class DoctrineProxyMatcher implements \ECSPrefix20210804\DeepCopy\Matcher\Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof \ECSPrefix20210804\Doctrine\Common\Persistence\Proxy;
    }
}
