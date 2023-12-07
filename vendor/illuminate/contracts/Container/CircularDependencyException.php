<?php

namespace ECSPrefix202312\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202312\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
