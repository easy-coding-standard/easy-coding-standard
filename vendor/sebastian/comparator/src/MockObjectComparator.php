<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/comparator.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\Comparator;

use ECSPrefix20210804\PHPUnit\Framework\MockObject\MockObject;
/**
 * Compares PHPUnit\Framework\MockObject\MockObject instances for equality.
 */
class MockObjectComparator extends \ECSPrefix20210804\SebastianBergmann\Comparator\ObjectComparator
{
    /**
     * Returns whether the comparator can compare two values.
     *
     * @param mixed $expected The first value to compare
     * @param mixed $actual   The second value to compare
     *
     * @return bool
     */
    public function accepts($expected, $actual)
    {
        return $expected instanceof \ECSPrefix20210804\PHPUnit\Framework\MockObject\MockObject && $actual instanceof \ECSPrefix20210804\PHPUnit\Framework\MockObject\MockObject;
    }
    /**
     * Converts an object to an array containing all of its private, protected
     * and public properties.
     *
     * @param object $object
     *
     * @return array
     */
    protected function toArray($object)
    {
        $array = parent::toArray($object);
        unset($array['__phpunit_invocationMocker']);
        return $array;
    }
}
