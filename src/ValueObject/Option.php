<?php

namespace Symplify\EasyCodingStandard\ValueObject;

final class Option
{
    /**
     * @var string
     */
    const FIX = 'fix';
    /**
     * @var string
     */
    const CLEAR_CACHE = 'clear-cache';
    /**
     * @var string
     */
    const NO_PROGRESS_BAR = 'no-progress-bar';
    /**
     * @var string
     */
    const NO_ERROR_TABLE = 'no-error-table';
    /**
     * @var string
     */
    const OUTPUT_FORMAT = 'output-format';
    /**
     * @api
     * @var string
     */
    const SKIP = 'skip';
    /**
     * @deprecated Use $containerConfigurator->import(...) instead
     * @var string
     */
    const SETS = 'sets';
    /**
     * @var string
     */
    const PATHS = 'paths';
    /**
     * @api
     * @var string
     */
    const ONLY = 'only';
    /**
     * @var string
     */
    const CACHE_DIRECTORY = 'cache_directory';
    /**
     * @var string
     */
    const LINE_ENDING = 'line_ending';
    /**
     * @var string
     */
    const INDENTATION = 'indentation';
    /**
     * @var string
     */
    const CACHE_NAMESPACE = 'cache_namespace';
    /**
     * @var string
     */
    const FILE_EXTENSIONS = 'file_extensions';
    /**
     * @var string
     */
    const INDENTATION_SPACES = 'spaces';
    /**
     * @api
     * @var string
     */
    const INDENTATION_TAB = 'tab';
    /**
     * @var string
     */
    const MATCH_GIT_DIFF = 'match-git-diff';
    /**
     * @var string
     */
    const XDEBUG = 'xdebug';
    /**
     * @var string
     */
    const DEBUG = 'debug';
}
