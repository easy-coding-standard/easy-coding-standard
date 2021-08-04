<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CliParser;

use function sprintf;
use RuntimeException;
final class OptionDoesNotAllowArgumentException extends \RuntimeException implements \ECSPrefix20210804\SebastianBergmann\CliParser\Exception
{
    public function __construct(string $option)
    {
        parent::__construct(\sprintf('Option "%s" does not allow an argument', $option));
    }
}
