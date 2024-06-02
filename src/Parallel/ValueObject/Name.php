<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

/**
 * Helpers constant for passing constant names around
 */
final class Name
{
    /**
     * @var string
     */
    public const LINE = 'line';
    /**
     * @var string
     */
    public const MESSAGE = 'message';
    /**
     * @var string
     */
    public const CHECKER_CLASS = 'checker_class';
    /**
     * @var string
     */
    public const RELATIVE_FILE_PATH = 'relative_file_path';
    /**
     * @var string
     */
    public const DIFF = 'diff';
    /**
     * @var string
     */
    public const DIFF_CONSOLE_FORMATTED = 'diff_console_formatted';
    /**
     * @var string
     */
    public const APPLIED_CHECKERS = 'applied_checkers';
}
