<?php

namespace ECSPrefix202309\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202309\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
