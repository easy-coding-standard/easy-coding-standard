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
    const LINE = 'line';
    /**
     * @var string
     */
    const MESSAGE = 'message';
    /**
     * @var string
     */
    const CHECKER_CLASS = 'checker_class';
    /**
     * @var string
     */
    const RELATIVE_FILE_PATH = 'relative_file_path';
    /**
     * @var string
     */
    const DIFF = 'diff';
    /**
     * @var string
     */
    const DIFF_CONSOLE_FORMATTED = 'diff_console_formatted';
    /**
     * @var string
     */
    const APPLIED_CHECKERS = 'applied_checkers';
}
