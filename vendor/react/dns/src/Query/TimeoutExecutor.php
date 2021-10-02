<?php

namespace ECSPrefix20211002\React\Dns\Query;

use ECSPrefix20211002\React\EventLoop\Loop;
use ECSPrefix20211002\React\EventLoop\LoopInterface;
use ECSPrefix20211002\React\Promise\Timer;
final class TimeoutExecutor implements \ECSPrefix20211002\React\Dns\Query\ExecutorInterface
{
    private $executor;
    private $loop;
    private $timeout;
    public function __construct(\ECSPrefix20211002\React\Dns\Query\ExecutorInterface $executor, $timeout, \ECSPrefix20211002\React\EventLoop\LoopInterface $loop = null)
    {
        $this->executor = $executor;
        $this->loop = $loop ?: \ECSPrefix20211002\React\EventLoop\Loop::get();
        $this->timeout = $timeout;
    }
    /**
     * @param \React\Dns\Query\Query $query
     */
    public function query($query)
    {
        return \ECSPrefix20211002\React\Promise\Timer\timeout($this->executor->query($query), $this->timeout, $this->loop)->then(null, function ($e) use($query) {
            if ($e instanceof \ECSPrefix20211002\React\Promise\Timer\TimeoutException) {
                $e = new \ECSPrefix20211002\React\Dns\Query\TimeoutException(\sprintf("DNS query for %s timed out", $query->describe()), 0, $e);
            }
            throw $e;
        });
    }
}
