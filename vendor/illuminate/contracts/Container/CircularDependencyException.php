<?php

namespace ECSPrefix202501\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202501\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
