<?php

namespace ECSPrefix202410\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202410\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
