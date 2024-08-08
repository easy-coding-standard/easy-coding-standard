<?php

namespace ECSPrefix202408\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202408\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
