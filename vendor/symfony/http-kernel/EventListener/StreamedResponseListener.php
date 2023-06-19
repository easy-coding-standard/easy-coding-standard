<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix202306\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix202306\Symfony\Component\HttpFoundation\StreamedResponse;
use ECSPrefix202306\Symfony\Component\HttpKernel\Event\ResponseEvent;
use ECSPrefix202306\Symfony\Component\HttpKernel\KernelEvents;
\ECSPrefix202306\trigger_deprecation('symfony/http-kernel', '6.1', 'The "%s" class is deprecated.', StreamedResponseListener::class);
/**
 * StreamedResponseListener is responsible for sending the Response
 * to the client.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 *
 * @deprecated since Symfony 6.1
 */
class StreamedResponseListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $response = $event->getResponse();
        if ($response instanceof StreamedResponse) {
            $response->send();
        }
    }
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', -1024]];
    }
}
