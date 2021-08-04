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
use ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException;
use Closure;
/**
 * Callback prediction.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallbackPrediction implements \ECSPrefix20210804\Prophecy\Prediction\PredictionInterface
{
    private $callback;
    /**
     * Initializes callback prediction.
     *
     * @param callable $callback Custom callback
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($callback)
    {
        if (!\is_callable($callback)) {
            throw new \ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException(\sprintf('Callable expected as an argument to CallbackPrediction, but got %s.', \gettype($callback)));
        }
        $this->callback = $callback;
    }
    /**
     * Executes preset callback.
     *
     * @param Call[]         $calls
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     */
    public function check(array $calls, \ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy $object, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $method)
    {
        $callback = $this->callback;
        if ($callback instanceof \Closure && \method_exists('Closure', 'bind')) {
            $callback = \Closure::bind($callback, $object);
        }
        \call_user_func($callback, $calls, $object, $method);
    }
}
