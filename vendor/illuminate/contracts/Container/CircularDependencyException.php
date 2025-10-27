<?php

namespace ECSPrefix202510\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202510\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
