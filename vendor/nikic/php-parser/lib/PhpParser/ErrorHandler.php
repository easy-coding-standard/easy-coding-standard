<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser;

interface ErrorHandler
{
    /**
     * Handle an error generated during lexing, parsing or some other operation.
     *
     * @param Error $error The error that needs to be handled
     */
    public function handleError(\ECSPrefix20210803\PhpParser\Error $error);
}
