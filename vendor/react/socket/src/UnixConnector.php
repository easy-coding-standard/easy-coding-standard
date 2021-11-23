<?php

namespace ECSPrefix20211123\React\Socket;

use ECSPrefix20211123\React\EventLoop\Loop;
use ECSPrefix20211123\React\EventLoop\LoopInterface;
use ECSPrefix20211123\React\Promise;
use InvalidArgumentException;
use RuntimeException;
/**
 * Unix domain socket connector
 *
 * Unix domain sockets use atomic operations, so we can as well emulate
 * async behavior.
 */
final class UnixConnector implements \ECSPrefix20211123\React\Socket\ConnectorInterface
{
    private $loop;
    public function __construct(\ECSPrefix20211123\React\EventLoop\LoopInterface $loop = null)
    {
        $this->loop = $loop ?: \ECSPrefix20211123\React\EventLoop\Loop::get();
    }
    public function connect($path)
    {
        if (\strpos($path, '://') === \false) {
            $path = 'unix://' . $path;
        } elseif (\substr($path, 0, 7) !== 'unix://') {
            return \ECSPrefix20211123\React\Promise\reject(new \InvalidArgumentException('Given URI "' . $path . '" is invalid'));
        }
        $resource = @\stream_socket_client($path, $errno, $errstr, 1.0);
        if (!$resource) {
            return \ECSPrefix20211123\React\Promise\reject(new \RuntimeException('Unable to connect to unix domain socket "' . $path . '": ' . $errstr, $errno));
        }
        $connection = new \ECSPrefix20211123\React\Socket\Connection($resource, $this->loop);
        $connection->unix = \true;
        return \ECSPrefix20211123\React\Promise\resolve($connection);
    }
}
