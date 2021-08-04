<?php

/*
 * This file is part of the Prophecy.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\Prophecy\Promise;

use ECSPrefix20210804\Doctrine\Instantiator\Instantiator;
use ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy;
use ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy;
use ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException;
use ReflectionClass;
/**
 * Throw promise.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ThrowPromise implements \ECSPrefix20210804\Prophecy\Promise\PromiseInterface
{
    private $exception;
    /**
     * @var \Doctrine\Instantiator\Instantiator
     */
    private $instantiator;
    /**
     * Initializes promise.
     *
     * @param string|\Exception|\Throwable $exception Exception class name or instance
     *
     * @throws \Prophecy\Exception\InvalidArgumentException
     */
    public function __construct($exception)
    {
        if (\is_string($exception)) {
            if (!\class_exists($exception) && !\interface_exists($exception) || !$this->isAValidThrowable($exception)) {
                throw new \ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException(\sprintf('Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.', $exception));
            }
        } elseif (!$exception instanceof \Exception && !$exception instanceof \Throwable) {
            throw new \ECSPrefix20210804\Prophecy\Exception\InvalidArgumentException(\sprintf('Exception / Throwable class or instance expected as argument to ThrowPromise, but got %s.', \is_object($exception) ? \get_class($exception) : \gettype($exception)));
        }
        $this->exception = $exception;
    }
    /**
     * Throws predefined exception.
     *
     * @param array          $args
     * @param ObjectProphecy $object
     * @param MethodProphecy $method
     *
     * @throws object
     */
    public function execute(array $args, \ECSPrefix20210804\Prophecy\Prophecy\ObjectProphecy $object, \ECSPrefix20210804\Prophecy\Prophecy\MethodProphecy $method)
    {
        if (\is_string($this->exception)) {
            $classname = $this->exception;
            $reflection = new \ReflectionClass($classname);
            $constructor = $reflection->getConstructor();
            if ($constructor->isPublic() && 0 == $constructor->getNumberOfRequiredParameters()) {
                throw $reflection->newInstance();
            }
            if (!$this->instantiator) {
                $this->instantiator = new \ECSPrefix20210804\Doctrine\Instantiator\Instantiator();
            }
            throw $this->instantiator->instantiate($classname);
        }
        throw $this->exception;
    }
    /**
     * @param string $exception
     *
     * @return bool
     */
    private function isAValidThrowable($exception)
    {
        return \is_a($exception, 'Exception', \true) || \is_a($exception, 'Throwable', \true);
    }
}
