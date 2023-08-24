<?php

namespace ECSPrefix202308\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202308\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
