<?php

namespace ECSPrefix202402\Illuminate\Contracts\Container;

use Exception;
use ECSPrefix202402\Psr\Container\ContainerExceptionInterface;
class CircularDependencyException extends Exception implements ContainerExceptionInterface
{
    //
}
