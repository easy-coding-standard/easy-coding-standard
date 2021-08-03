<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\Util;

use const PHP_EOL;
use function get_class;
use function sprintf;
use function str_replace;
use ECSPrefix20210803\PHPUnit\Framework\TestCase;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use ECSPrefix20210803\PHPUnit\Runner\PhptTestCase;
use RecursiveIteratorIterator;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TextTestListRenderer
{
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function render(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : string
    {
        $buffer = 'Available test(s):' . \PHP_EOL;
        foreach (new \RecursiveIteratorIterator($suite->getIterator()) as $test) {
            if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                $name = \sprintf('%s::%s', \get_class($test), \str_replace(' with data set ', '', $test->getName()));
            } elseif ($test instanceof \ECSPrefix20210803\PHPUnit\Runner\PhptTestCase) {
                $name = $test->getName();
            } else {
                continue;
            }
            $buffer .= \sprintf(' - %s' . \PHP_EOL, $name);
        }
        return $buffer;
    }
}
