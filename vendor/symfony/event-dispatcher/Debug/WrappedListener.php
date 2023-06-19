<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\EventDispatcher\Debug;

use ECSPrefix202306\Psr\EventDispatcher\StoppableEventInterface;
use ECSPrefix202306\Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ECSPrefix202306\Symfony\Component\Stopwatch\Stopwatch;
use ECSPrefix202306\Symfony\Component\VarDumper\Caster\ClassStub;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class WrappedListener
{
    /**
     * @var string|mixed[]|object
     */
    private $listener;
    /**
     * @var \Closure|null
     */
    private $optimizedListener;
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $called = \false;
    /**
     * @var bool
     */
    private $stoppedPropagation = \false;
    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    private $stopwatch;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface|null
     */
    private $dispatcher;
    /**
     * @var string
     */
    private $pretty;
    /**
     * @var string
     */
    private $callableRef;
    /**
     * @var \Symfony\Component\VarDumper\Caster\ClassStub|string
     */
    private $stub;
    /**
     * @var int|null
     */
    private $priority;
    /**
     * @var bool
     */
    private static $hasClassStub;
    /**
     * @param callable|mixed[] $listener
     */
    public function __construct($listener, ?string $name, Stopwatch $stopwatch, EventDispatcherInterface $dispatcher = null, int $priority = null)
    {
        $this->listener = $listener;
        $this->optimizedListener = $listener instanceof \Closure ? $listener : (\is_callable($listener) ? \Closure::fromCallable($listener) : null);
        $this->stopwatch = $stopwatch;
        $this->dispatcher = $dispatcher;
        $this->priority = $priority;
        if (\is_array($listener)) {
            [$this->name, $this->callableRef] = $this->parseListener($listener);
            $this->pretty = $this->name . '::' . $listener[1];
            $this->callableRef .= '::' . $listener[1];
        } elseif ($listener instanceof \Closure) {
            $r = new \ReflectionFunction($listener);
            if (\strpos($r->name, '{closure}') !== \false) {
                $this->pretty = $this->name = 'closure';
            } elseif ($class = \PHP_VERSION_ID >= 80111 ? $r->getClosureCalledClass() : $r->getClosureScopeClass()) {
                $this->name = $class->name;
                $this->pretty = $this->name . '::' . $r->name;
            } else {
                $this->pretty = $this->name = $r->name;
            }
        } elseif (\is_string($listener)) {
            $this->pretty = $this->name = $listener;
        } else {
            $this->name = \get_debug_type($listener);
            $this->pretty = $this->name . '::__invoke';
            $this->callableRef = \get_class($listener) . '::__invoke';
        }
        if (null !== $name) {
            $this->name = $name;
        }
        self::$hasClassStub = self::$hasClassStub ?? \class_exists(ClassStub::class);
    }
    /**
     * @return callable|mixed[]
     */
    public function getWrappedListener()
    {
        return $this->listener;
    }
    public function wasCalled() : bool
    {
        return $this->called;
    }
    public function stoppedPropagation() : bool
    {
        return $this->stoppedPropagation;
    }
    public function getPretty() : string
    {
        return $this->pretty;
    }
    public function getInfo(string $eventName) : array
    {
        $this->stub = $this->stub ?? (self::$hasClassStub ? new ClassStub($this->pretty . '()', $this->callableRef ?? $this->listener) : $this->pretty . '()');
        return ['event' => $eventName, 'priority' => $this->priority = $this->priority ?? (($dispatcher = $this->dispatcher) ? $dispatcher->getListenerPriority($eventName, $this->listener) : null), 'pretty' => $this->pretty, 'stub' => $this->stub];
    }
    public function __invoke(object $event, string $eventName, EventDispatcherInterface $dispatcher) : void
    {
        $dispatcher = $this->dispatcher ?: $dispatcher;
        $this->called = \true;
        $this->priority = $this->priority ?? $dispatcher->getListenerPriority($eventName, $this->listener);
        $e = $this->stopwatch->start($this->name, 'event_listener');
        try {
            ($this->optimizedListener ?? $this->listener)($event, $eventName, $dispatcher);
        } finally {
            if ($e->isStarted()) {
                $e->stop();
            }
        }
        if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
            $this->stoppedPropagation = \true;
        }
    }
    private function parseListener(array $listener) : array
    {
        if ($listener[0] instanceof \Closure) {
            foreach (\method_exists(new \ReflectionFunction($listener[0]), 'getAttributes') ? (new \ReflectionFunction($listener[0]))->getAttributes(\Closure::class) : [] as $attribute) {
                if ($name = $attribute->getArguments()['name'] ?? \false) {
                    return [$name, $attribute->getArguments()['class'] ?? $name];
                }
            }
        }
        if (\is_object($listener[0])) {
            return [\get_debug_type($listener[0]), \get_class($listener[0])];
        }
        return [$listener[0], $listener[0]];
    }
}
