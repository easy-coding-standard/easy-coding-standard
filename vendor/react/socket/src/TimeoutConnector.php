<?php

namespace ECSPrefix20220220\React\Socket;

use ECSPrefix20220220\React\EventLoop\Loop;
use ECSPrefix20220220\React\EventLoop\LoopInterface;
use ECSPrefix20220220\React\Promise\Timer;
use ECSPrefix20220220\React\Promise\Timer\TimeoutException;
final class TimeoutConnector implements \ECSPrefix20220220\React\Socket\ConnectorInterface
{
    private $connector;
    private $timeout;
    private $loop;
    public function __construct(\ECSPrefix20220220\React\Socket\ConnectorInterface $connector, $timeout, \ECSPrefix20220220\React\EventLoop\LoopInterface $loop = null)
    {
        $this->connector = $connector;
        $this->timeout = $timeout;
        $this->loop = $loop ?: \ECSPrefix20220220\React\EventLoop\Loop::get();
    }
    public function connect($uri)
    {
        return \ECSPrefix20220220\React\Promise\Timer\timeout($this->connector->connect($uri), $this->timeout, $this->loop)->then(null, self::handler($uri));
    }
    /**
     * Creates a static rejection handler that reports a proper error message in case of a timeout.
     *
     * This uses a private static helper method to ensure this closure is not
     * bound to this instance and the exception trace does not include a
     * reference to this instance and its connector stack as a result.
     *
     * @param string $uri
     * @return callable
     */
    private static function handler($uri)
    {
        return function (\Exception $e) use($uri) {
            if ($e instanceof \ECSPrefix20220220\React\Promise\Timer\TimeoutException) {
                throw new \RuntimeException('Connection to ' . $uri . ' timed out after ' . $e->getTimeout() . ' seconds (ETIMEDOUT)', \defined('SOCKET_ETIMEDOUT') ? \SOCKET_ETIMEDOUT : 110);
            }
            throw $e;
        };
    }
}
