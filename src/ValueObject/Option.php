<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject;

final class Option
{
    /**
     * @var string
     */
    public const FIX = 'fix';

    /**
     * @var string
     */
    public const CLEAR_CACHE = 'clear-cache';

    /**
     * @var string
     */
    public const NO_PROGRESS_BAR = 'no-progress-bar';

    /**
     * @var string
     */
    public const NO_ERROR_TABLE = 'no-error-table';

    /**
     * @var string
     */
    public const OUTPUT_FORMAT = 'output-format';

    /**
     * @api
     * @var string
     */
    public const SKIP = 'skip';

    /**
     * @deprecated Use $containerConfigurator->import(...) instead
     * @var string
     */
    public const SETS = 'sets';

    /**
     * @var string
     */
    public const PATHS = 'paths';

    /**
     * @api
     * @var string
     */
    public const ONLY = 'only';

    /**
     * @var string
     */
    public const CACHE_DIRECTORY = 'cache_directory';

    /**
     * @var string
     */
    public const LINE_ENDING = 'line_ending';

    /**
     * @var string
     */
    public const INDENTATION = 'indentation';

    /**
     * @var string
     */
    public const CACHE_NAMESPACE = 'cache_namespace';

    /**
     * @var string
     */
    public const FILE_EXTENSIONS = 'file_extensions';

    /**
     * @var string
     */
    public const INDENTATION_SPACES = 'spaces';

    /**
     * @api
     * @var string
     */
    public const INDENTATION_TAB = 'tab';

    /**
     * @var string
     */
    public const MATCH_GIT_DIFF = 'match-git-diff';

    /**
     * @var string
     */
    public const XDEBUG = 'xdebug';

    /**
     * @var string
     */
    public const DEBUG = 'debug';
}
