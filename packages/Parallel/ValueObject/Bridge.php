<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Parallel\ValueObject;

/**
 * @enum
 */
final class Bridge
{
    /**
     * @var string
     */
    const FILE_DIFFS = 'file_diffs';
    /**
     * @var string
     */
    const CODING_STANDARD_ERRORS = 'coding_standard_errors';
    /**
     * @var string
     */
    const SYSTEM_ERRORS = 'system_errors';
    /**
     * @var string
     */
    const SYSTEM_ERRORS_COUNT = 'system_errors_count';
    /**
     * @var string
     */
    const FILES = 'files';
    /**
     * @var string
     */
    const FILES_COUNT = 'files_count';
}
