<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210512\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix20210512\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix20210512\Symfony\Component\HttpKernel\Event\ResponseEvent;
use ECSPrefix20210512\Symfony\Component\HttpKernel\KernelEvents;
/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ResponseListener implements \ECSPrefix20210512\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $charset;
    /**
     * @param string $charset
     */
    public function __construct($charset)
    {
        $charset = (string) $charset;
        $this->charset = $charset;
    }
    /**
     * Filters the Response.
     */
    public function onKernelResponse(\ECSPrefix20210512\Symfony\Component\HttpKernel\Event\ResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $response = $event->getResponse();
        if (null === $response->getCharset()) {
            $response->setCharset($this->charset);
        }
        $response->prepare($event->getRequest());
    }
    /**
     * @return mixed[]
     */
    public static function getSubscribedEvents()
    {
        return [\ECSPrefix20210512\Symfony\Component\HttpKernel\KernelEvents::RESPONSE => 'onKernelResponse'];
    }
}
