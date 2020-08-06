<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration;

final class Option
{
    /**
     * @var string
     */
    public const SOURCE = 'source';

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
     * @var string
     */
    public const SKIP = 'skip';

    /**
     * @var string
     */
    public const EXCLUDE_PATHS = 'exclude_paths';

    /**
     * @var string
     * @deprecated Use self::EXCLUDE_PATHS
     */
    public const EXCLUDE_FILES = 'exclude_files';

    /**
     * @var string
     */
    public const SETS = 'sets';

    /**
     * @var string
     */
    public const PATHS = 'paths';

    /**
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
    public const INDENTATION_TABS = 'tabs';
}
