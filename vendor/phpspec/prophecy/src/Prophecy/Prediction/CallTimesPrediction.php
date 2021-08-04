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
use ECSPrefix20210804\Prophecy\Exception\Prediction\UnexpectedCallsCountException;
/**
 * Prediction interface.
 * Predictions are logical test blocks, tied to `should...` keyword.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallTimesPrediction implements \ECSPrefix20210804\Prophecy\Prediction\PredictionInterface
{
    private $times;
    private $util;
    /**
     * Initializes prediction.
     *
     * @param int        $times
     * @param StringUtil $util
     */
    public function __construct($times, \ECSPrefix20210804\Prophecy\Util\StringUtil $util = null)
    {
        $this->times = \intval($times);
        $this->util = $util ?: new \ECSPrefix20210804\Prophecy\Util\StringUtil();
    }
    /**
     * Tests that there was exact amount of calls made.
     *
     * @param Call[]         $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws \Prophecy\Exception\Prediction\UnexpectedCallsCountException
     */
    public function check(array $calls, \ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy $object, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $method)
    {
        if ($this->times == \count($calls)) {
            return;
        }
        $methodCalls = $object->findProphecyMethodCalls($method->getMethodName(), new \ECSPrefix20210804\Prophecy\Argument\ArgumentsWildcard(array(new \ECSPrefix20210804\Prophecy\Argument\Token\AnyValuesToken())));
        if (\count($calls)) {
            $message = \sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . "but %d were made:\n%s", $this->times, \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard(), \count($calls), $this->util->stringifyCalls($calls));
        } elseif (\count($methodCalls)) {
            $message = \sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . "but none were made.\n" . "Recorded `%s(...)` calls:\n%s", $this->times, \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard(), $method->getMethodName(), $this->util->stringifyCalls($methodCalls));
        } else {
            $message = \sprintf("Expected exactly %d calls that match:\n" . "  %s->%s(%s)\n" . "but none were made.", $this->times, \get_class($object->reveal()), $method->getMethodName(), $method->getArgumentsWildcard());
        }
        throw new \ECSPrefix20210804\Prophecy\Exception\Prediction\UnexpectedCallsCountException($message, $method, $this->times, $calls);
    }
}
