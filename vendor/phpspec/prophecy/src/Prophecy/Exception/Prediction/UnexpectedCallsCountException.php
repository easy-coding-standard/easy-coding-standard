<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Exception\Prediction;

use ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy;
class UnexpectedCallsCountException extends \ECSPrefix20210804\Prophecy\Exception\Prediction\UnexpectedCallsException
{
    private $expectedCount;
    public function __construct($message, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $methodProphecy, $count, array $calls)
    {
        parent::__construct($message, $methodProphecy, $calls);
        $this->expectedCount = \intval($count);
    }
    public function getExpectedCount()
    {
        return $this->expectedCount;
    }
}
