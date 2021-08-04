<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Argument\Token;

use ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException;
/**
 * Callback-verified token.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CallbackToken implements \ECSPrefix20210804\Prophecy\Argument\Token\TokenInterface
{
    private $callback;
    /**
     * Initializes token.
     *
     * @param callable $callback
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($callback)
    {
        if (!\is_callable($callback)) {
            throw new \ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException(\sprintf('Callable expected as an argument to CallbackToken, but got %s.', \gettype($callback)));
        }
        $this->callback = $callback;
    }
    /**
     * Scores 7 if callback returns true, false otherwise.
     *
     * @param $argument
     *
     * @return bool|int
     */
    public function scoreArgument($argument)
    {
        return \call_user_func($this->callback, $argument) ? 7 : \false;
    }
    /**
     * Returns false.
     *
     * @return bool
     */
    public function isLast()
    {
        return \false;
    }
    /**
     * Returns string representation for token.
     *
     * @return string
     */
    public function __toString()
    {
        return 'callback()';
    }
}
