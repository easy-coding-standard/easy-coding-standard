<?php

/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\SebastianBergmann\Diff;

use function get_class;
use function gettype;
use function is_object;
use function sprintf;
use Exception;
final class ConfigurationException extends \ECSPrefix20210508\SebastianBergmann\Diff\InvalidArgumentException
{
    /**
     * @param string $option
     */
    public function __construct($option, string $expected, $value, int $code = 0, \Exception $previous = null)
    {
        if (\is_object($option)) {
            $option = (string) $option;
        }
        parent::__construct(\sprintf('Option "%s" must be %s, got "%s".', $option, $expected, \is_object($value) ? \get_class($value) : (null === $value ? '<null>' : \gettype($value) . '#' . $value)), $code, $previous);
    }
}
