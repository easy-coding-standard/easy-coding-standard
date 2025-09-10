<?php

namespace ECSPrefix202509\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202509\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
