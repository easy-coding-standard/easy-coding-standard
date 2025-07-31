<?php

namespace ECSPrefix202507\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202507\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
