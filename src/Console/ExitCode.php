<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console;

final class ExitCode
{
    /**
     * @var int
     */
    public const SUCCESS = 0;
    /**
     * @var int
     */
    public const FAILURE = 1;
    /**
     * @var int
     */
    public const CHANGED_CODE_OR_FOUND_ERRORS = 2;
}
