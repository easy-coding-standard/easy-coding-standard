<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy\Comparator;

use ECSPrefix20210803\Prophecy\Prophecy\ProphecyInterface;
use ECSPrefix20210803\SebastianBergmann\Comparator\ObjectComparator;
class ProphecyComparator extends \ECSPrefix20210803\SebastianBergmann\Comparator\ObjectComparator
{
    public function accepts($expected, $actual)
    {
        return \is_object($expected) && \is_object($actual) && $actual instanceof \ECSPrefix20210803\Prophecy\Prophecy\ProphecyInterface;
    }
    public function assertEquals($expected, $actual, $delta = 0.0, $canonicalize = \false, $ignoreCase = \false, array &$processed = array())
    {
        parent::assertEquals($expected, $actual->reveal(), $delta, $canonicalize, $ignoreCase, $processed);
    }
}
