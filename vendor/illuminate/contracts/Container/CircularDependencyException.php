<?php

namespace ECSPrefix202307\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202307\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
