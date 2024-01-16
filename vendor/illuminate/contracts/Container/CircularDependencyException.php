<?php

namespace ECSPrefix202401\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202401\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
