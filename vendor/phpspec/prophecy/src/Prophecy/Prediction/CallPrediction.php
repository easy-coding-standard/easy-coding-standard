<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Prediction;

use ECSPrefix20210804\Prophecy\Call\Call;
use ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy;
use ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy;
use ECSPrefix20210804\Prophecy\Argument\ArgumentsWildcard;
use ECSPrefix20210804\Prophecy\Argument\Token\AnyValuesToken;
use ECSPrefix20210804\Prophecy\Util\StringUtil;
use ECSPrefix20210804\Prophecy\Exception\Prediction\NoCallsException;
/**
 * Call prediction.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallPrediction implements \ECSPrefix20210804\Prophecy\Prediction\PredictionInterface
{
    private $util;
    /**
     * Initializes prediction.
     *
     * @param StringUtil $util
     */
    public function __construct(\ECSPrefix20210804\Prophecy\Util\StringUtil $util = null)
    {
        $this->util = $util ?: new \ECSPrefix20210804\Prophecy\Util\StringUtil();
    }
    /**
     * Tests that there was at least one call.
     *
     * @param Call[]         $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\NoCallsException
     */
    public function check(array $calls, \ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy $object, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $method)
    {
        if (\count($calls)) {
            return;
        }
        $methodCalls = $object->findProphecyMethodCalls($method->getMethodName(), new \ECSPrefix20210804\Prophecy\Argument\ArgumentsWildcard(array(new \ECSPrefix20210804\Prophecy\Argument\Token\AnyValuesToken())));
        if (\count($methodCalls)) {
            throw new \ECSPrefix20210804\Prophecy\Exception\Prediction\NoCallsException(\sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . "but expected at least one.\n" . "Recorded `%s(...)` calls:\n%s", \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard(), $method->getMethodName(), $this->util->stringifyCalls($methodCalls)), $method);
        }
        throw new \ECSPrefix20210804\Prophecy\Exception\Prediction\NoCallsException(\sprintf("No calls have been made that match:\n" . "  %s->%s(%s)\n" . "but expected at least one.", \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard()), $method);
    }
}
