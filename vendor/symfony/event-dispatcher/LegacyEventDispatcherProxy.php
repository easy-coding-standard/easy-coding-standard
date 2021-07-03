<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210703\Symfony\Component\EventDispatcher;

use ECSPrefix20210703\Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
trigger_deprecation('symfony/event-dispatcher', '5.1', '%s is deprecated, use the event dispatcher without the proxy.', \ECSPrefix20210703\Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy::class);
/**
 * A helper class to provide BC/FC with the legacy signature of EventDispatcherInterface::dispatch().
 *
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @deprecated since Symfony 5.1
 */
final class LegacyEventDispatcherProxy
{
    /**
     * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null $dispatcher
     * @return \Symfony\Contracts\EventDispatcher\EventDispatcherInterface|null
     */
    public static function decorate($dispatcher)
    {
        return $dispatcher;
    }
}
