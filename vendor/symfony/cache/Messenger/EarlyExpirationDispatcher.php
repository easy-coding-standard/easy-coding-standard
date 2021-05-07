<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Messenger;

use ECSPrefix20210507\Psr\Log\LoggerInterface;
use ECSPrefix20210507\Symfony\Component\Cache\Adapter\AdapterInterface;
use ECSPrefix20210507\Symfony\Component\Cache\CacheItem;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ReverseContainer;
use ECSPrefix20210507\Symfony\Component\Messenger\MessageBusInterface;
use ECSPrefix20210507\Symfony\Component\Messenger\Stamp\HandledStamp;
/**
 * Sends the computation of cached values to a message bus.
 */
class EarlyExpirationDispatcher
{
    private $bus;
    private $reverseContainer;
    private $callbackWrapper;
    /**
     * @param \Symfony\Component\Messenger\MessageBusInterface $bus
     * @param \Symfony\Component\DependencyInjection\ReverseContainer $reverseContainer
     */
    public function __construct($bus, $reverseContainer, callable $callbackWrapper = null)
    {
        $this->bus = $bus;
        $this->reverseContainer = $reverseContainer;
        $this->callbackWrapper = $callbackWrapper;
    }
    /**
     * @param \Symfony\Component\Cache\CacheItem $item
     * @param bool $save
     * @param \Symfony\Component\Cache\Adapter\AdapterInterface $pool
     * @param \Closure $setMetadata
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __invoke(callable $callback, $item, &$save, $pool, $setMetadata, $logger = null)
    {
        if (!$item->isHit() || null === ($message = \ECSPrefix20210507\Symfony\Component\Cache\Messenger\EarlyExpirationMessage::create($this->reverseContainer, $callback, $item, $pool))) {
            // The item is stale or the callback cannot be reversed: we must compute the value now
            $logger && $logger->info('Computing item "{key}" online: ' . ($item->isHit() ? 'callback cannot be reversed' : 'item is stale'), ['key' => $item->getKey()]);
            return null !== $this->callbackWrapper ? ($this->callbackWrapper)($callback, $item, $save, $pool, $setMetadata, $logger) : $callback($item, $save);
        }
        $envelope = $this->bus->dispatch($message);
        if ($logger) {
            if ($envelope->last(\ECSPrefix20210507\Symfony\Component\Messenger\Stamp\HandledStamp::class)) {
                $logger->info('Item "{key}" was computed online', ['key' => $item->getKey()]);
            } else {
                $logger->info('Item "{key}" sent for recomputation', ['key' => $item->getKey()]);
            }
        }
        // The item's value is not stale, no need to write it to the backend
        $save = \false;
        return $message->getItem()->get() !== null ? $message->getItem()->get() : $item->get();
    }
}
