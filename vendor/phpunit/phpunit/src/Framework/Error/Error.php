<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\Framework\Error;

use ECSPrefix20210804\PHPUnit\Framework\Exception;
/**
 * @internal
 */
class Error extends \ECSPrefix20210804\PHPUnit\Framework\Exception
{
    public function __construct(string $message, int $code, string $file, int $line, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->file = $file;
        $this->line = $line;
    }
}
