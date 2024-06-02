<?php

namespace ECSPrefix202406\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202406\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
