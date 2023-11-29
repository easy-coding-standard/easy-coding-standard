<?php

namespace ECSPrefix202311\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202311\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
