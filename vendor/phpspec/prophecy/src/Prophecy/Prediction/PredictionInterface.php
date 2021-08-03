<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\Prophecy\Prediction;

use ECSPrefix20210803\Prophecy\Call\Call;
use ECSPrefix20210803\Prophecy\Prophecy\ObjectProphecy;
use ECSPrefix20210803\Prophecy\Prophecy\MethodProphecy;
/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
interface PredictionInterface
{
    /**
     * Tests that double fulfilled prediction.
     *
     * @param Call[]        $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws object
     * @return void
     */
    public function check(array $calls, \ECSPrefix20210803\Prophecy\Prophecy\ObjectProphecy $object, \ECSPrefix20210803\Prophecy\Prophecy\MethodProphecy $method);
}
