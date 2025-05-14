<?php

namespace ECSPrefix202505\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202505\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
