<?php

namespace ECSPrefix20210803\DeepCopy\Matcher\Doctrine;

use ECSPrefix20210803\DeepCopy\Matcher\Matcher;
use ECSPrefix20210803\Doctrine\Common\Persistence\Proxy;
/**
 * @final
 */
class DoctrineProxyMatcher implements \ECSPrefix20210803\DeepCopy\Matcher\Matcher
{
    /**
     * Matches a Doctrine Proxy class.
     *
     * {@inheritdoc}
     */
    public function matches($object, $property)
    {
        return $object instanceof \ECSPrefix20210803\Doctrine\Common\Persistence\Proxy;
    }
}
