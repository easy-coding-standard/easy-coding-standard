<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\ErrorHandler;

use ECSPrefix20210803\PhpParser\Error;
use ECSPrefix20210803\PhpParser\ErrorHandler;
/**
 * Error handler that handles all errors by throwing them.
 *
 * This is the default strategy used by all components.
 */
class Throwing implements \ECSPrefix20210803\PhpParser\ErrorHandler
{
    public function handleError(\ECSPrefix20210803\PhpParser\Error $error)
    {
        throw $error;
    }
}
