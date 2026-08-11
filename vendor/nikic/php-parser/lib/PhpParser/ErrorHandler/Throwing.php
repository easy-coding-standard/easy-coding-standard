<?php

declare (strict_types=1);
namespace ECSPrefix202608\PhpParser\ErrorHandler;

use ECSPrefix202608\PhpParser\Error;
use ECSPrefix202608\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements ErrorHandler
{
    public function handleError(Error $error): void
    {
        throw $error;
    }
}
