<?php

namespace ECSPrefix202606\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202606\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
