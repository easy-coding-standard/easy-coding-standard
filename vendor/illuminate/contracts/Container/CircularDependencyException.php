<?php

namespace ECSPrefix202412\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202412\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
