<?php

namespace ECSPrefix202504\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202504\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
