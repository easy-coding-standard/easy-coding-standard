<?php

namespace ECSPrefix202503\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202503\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
