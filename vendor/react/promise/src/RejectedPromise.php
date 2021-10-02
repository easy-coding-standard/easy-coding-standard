<?php

namespace ECSPrefix20211002\React\Promise;

/**
 * @deprecated 2.8.0 External usage of RejectedPromise is deprecated, use `reject()` instead.
 */
class RejectedPromise implements \ECSPrefix20211002\React\Promise\ExtendedPromiseInterface, \ECSPrefix20211002\React\Promise\CancellablePromiseInterface
{
    private $reason;
    public function __construct($reason = null)
    {
        if ($reason instanceof \ECSPrefix20211002\React\Promise\PromiseInterface) {
            throw new \InvalidArgumentException('You cannot create React\\Promise\\RejectedPromise with a promise. Use React\\Promise\\reject($promiseOrValue) instead.');
        }
        $this->reason = $reason;
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     */
    public function then($onFulfilled = null, $onRejected = null, $onProgress = null)
    {
        if (null === $onRejected) {
            return $this;
        }
        try {
            return resolve($onRejected($this->reason));
        } catch (\Throwable $exception) {
            return new \ECSPrefix20211002\React\Promise\RejectedPromise($exception);
        } catch (\Exception $exception) {
            return new \ECSPrefix20211002\React\Promise\RejectedPromise($exception);
        }
    }
    /**
     * @param callable|null $onFulfilled
     * @param callable|null $onRejected
     * @param callable|null $onProgress
     */
    public function done($onFulfilled = null, $onRejected = null, $onProgress = null)
    {
        if (null === $onRejected) {
            throw \ECSPrefix20211002\React\Promise\UnhandledRejectionException::resolve($this->reason);
        }
        $result = $onRejected($this->reason);
        if ($result instanceof self) {
            throw \ECSPrefix20211002\React\Promise\UnhandledRejectionException::resolve($result->reason);
        }
        if ($result instanceof \ECSPrefix20211002\React\Promise\ExtendedPromiseInterface) {
            $result->done();
        }
    }
    /**
     * @param callable $onRejected
     */
    public function otherwise($onRejected)
    {
        if (!_checkTypehint($onRejected, $this->reason)) {
            return $this;
        }
        return $this->then(null, $onRejected);
    }
    /**
     * @param callable $onFulfilledOrRejected
     */
    public function always($onFulfilledOrRejected)
    {
        return $this->then(null, function ($reason) use($onFulfilledOrRejected) {
            return resolve($onFulfilledOrRejected())->then(function () use($reason) {
                return new \ECSPrefix20211002\React\Promise\RejectedPromise($reason);
            });
        });
    }
    /**
     * @param callable $onProgress
     */
    public function progress($onProgress)
    {
        return $this;
    }
    public function cancel()
    {
    }
}
