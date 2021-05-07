<?php

namespace ECSPrefix20210507\Nette\Utils;

/**
 * The exception that is thrown when an image error occurs.
 */
class ImageException extends \Exception
{
}
/**
 * The exception that indicates invalid image file.
 */
class UnknownImageFileException extends \ECSPrefix20210507\Nette\Utils\ImageException
{
}
/**
 * The exception that indicates error of JSON encoding/decoding.
 */
class JsonException extends \Exception
{
}
/**
 * The exception that indicates error of the last Regexp execution.
 */
class RegexpException extends \Exception
{
    const MESSAGES = [\PREG_INTERNAL_ERROR => 'Internal error', \PREG_BACKTRACK_LIMIT_ERROR => 'Backtrack limit was exhausted', \PREG_RECURSION_LIMIT_ERROR => 'Recursion limit was exhausted', \PREG_BAD_UTF8_ERROR => 'Malformed UTF-8 data', \PREG_BAD_UTF8_OFFSET_ERROR => 'Offset didn\'t correspond to the begin of a valid UTF-8 code point', 6 => 'Failed due to limited JIT stack space'];
}
/**
 * The exception that indicates assertion error.
 */
class AssertionException extends \Exception
{
}
