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
use ECSPrefix20210804\Prophecy\Exception\Prophecy\MethodProphecyException;
class UnexpectedCallsException extends \ECSPrefix20210804\Prophecy\Exception\Prophecy\MethodProphecyException implements \ECSPrefix20210804\Prophecy\Exception\Prediction\PredictionException
{
    private $calls = array();
    public function __construct($message, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $methodProphecy, array $calls)
    {
        parent::__construct($message, $methodProphecy);
        $this->calls = $calls;
    }
    public function getCalls()
    {
        return $this->calls;
    }
}
