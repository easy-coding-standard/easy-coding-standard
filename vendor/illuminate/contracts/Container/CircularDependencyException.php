<?php

namespace ECSPrefix202405\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202405\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
