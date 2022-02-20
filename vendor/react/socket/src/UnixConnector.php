<?php

namespace ECSPrefix20220220\React\Socket;

use ECSPrefix20220220\React\EventLoop\Loop;
use ECSPrefix20220220\React\EventLoop\LoopInterface;
use ECSPrefix20220220\React\Promise;
use InvalidArgumentException;
use RuntimeException;
/**
 * Unix domain socket connector
 *
 * Unix domain sockets use atomic operations, so we can as well emulate
 * async behavior.
 */
final class UnixConnector implements \ECSPrefix20220220\React\Socket\ConnectorInterface
{
    private $loop;
    public function __construct(\ECSPrefix20220220\React\EventLoop\LoopInterface $loop = null)
    {
        $this->loop = $loop ?: \ECSPrefix20220220\React\EventLoop\Loop::get();
    }
    public function connect($path)
    {
        if (\strpos($path, '://') === \false) {
            $path = 'unix://' . $path;
        } elseif (\substr($path, 0, 7) !== 'unix://') {
            return \ECSPrefix20220220\React\Promise\reject(new \InvalidArgumentException('Given URI "' . $path . '" is invalid (EINVAL)', \defined('SOCKET_EINVAL') ? \SOCKET_EINVAL : 22));
        }
        $resource = @\stream_socket_client($path, $errno, $errstr, 1.0);
        if (!$resource) {
            return \ECSPrefix20220220\React\Promise\reject(new \RuntimeException('Unable to connect to unix domain socket "' . $path . '": ' . $errstr . \ECSPrefix20220220\React\Socket\SocketServer::errconst($errno), $errno));
        }
        $connection = new \ECSPrefix20220220\React\Socket\Connection($resource, $this->loop);
        $connection->unix = \true;
        return \ECSPrefix20220220\React\Promise\resolve($connection);
    }
}
