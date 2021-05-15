<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210515\Symfony\Component\EventDispatcher;

/**
 * A read-only proxy for an event dispatcher.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class ImmutableEventDispatcher implements \ECSPrefix20210515\Symfony\Component\EventDispatcher\EventDispatcherInterface
{
    private $dispatcher;
    public function __construct(\ECSPrefix20210515\Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    /**
     * {@inheritdoc}
     * @param string|null $eventName
     * @param object $event
     * @return object
     */
    public function dispatch($event, $eventName = null)
    {
        return $this->dispatcher->dispatch($event, $eventName);
    }
    /**
     * {@inheritdoc}
     * @param string $eventName
     * @param int $priority
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $eventName = (string) $eventName;
        $priority = (int) $priority;
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * {@inheritdoc}
     */
    public function addSubscriber(\ECSPrefix20210515\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * {@inheritdoc}
     * @param string $eventName
     */
    public function removeListener($eventName, $listener)
    {
        $eventName = (string) $eventName;
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * {@inheritdoc}
     */
    public function removeSubscriber(\ECSPrefix20210515\Symfony\Component\EventDispatcher\EventSubscriberInterface $subscriber)
    {
        throw new \BadMethodCallException('Unmodifiable event dispatchers must not be modified.');
    }
    /**
     * {@inheritdoc}
     * @param string $eventName
     */
    public function getListeners($eventName = null)
    {
        return $this->dispatcher->getListeners($eventName);
    }
    /**
     * {@inheritdoc}
     * @param string $eventName
     */
    public function getListenerPriority($eventName, $listener)
    {
        $eventName = (string) $eventName;
        return $this->dispatcher->getListenerPriority($eventName, $listener);
    }
    /**
     * {@inheritdoc}
     * @param string $eventName
     */
    public function hasListeners($eventName = null)
    {
        return $this->dispatcher->hasListeners($eventName);
    }
}
