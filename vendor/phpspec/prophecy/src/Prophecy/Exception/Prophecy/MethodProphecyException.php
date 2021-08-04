<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Exception\Prophecy;

use ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy;
class MethodProphecyException extends \ECSPrefix20210804\Prophecy\Exception\Prophecy\ObjectProphecyException
{
    private $methodProphecy;
    public function __construct($message, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $methodProphecy)
    {
        parent::__construct($message, $methodProphecy->getObjectProphecy());
        $this->methodProphecy = $methodProphecy;
    }
    /**
     * @return MethodProphecy
     */
    public function getMethodProphecy()
    {
        return $this->methodProphecy;
    }
}
