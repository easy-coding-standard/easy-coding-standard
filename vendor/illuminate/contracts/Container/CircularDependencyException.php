<?php

namespace ECSPrefix202508\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202508\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
