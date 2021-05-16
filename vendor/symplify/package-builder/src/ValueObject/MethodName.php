<?php

namespace ECSPrefix20210516\Symplify\PackageBuilder\ValueObject;

final class MethodName
{
    /**
     * @var string
     */
    const CONSTRUCTOR = '__construct';
    /**
     * @var string
     */
    const SET_UP = 'setUp';
    /**
     * @var string
     */
    const INVOKE = '__invoke';
}
