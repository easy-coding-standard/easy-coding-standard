<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\HttpKernel\EventListener;

use ConfigTransformer20210601\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\Event\ResponseEvent;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\KernelEvents;
/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ResponseListener implements \ConfigTransformer20210601\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $charset;
    public function __construct(string $charset)
    {
        $this->charset = $charset;
    }
    /**
     * Filters the Response.
     */
    public function onKernelResponse(\ConfigTransformer20210601\Symfony\Component\HttpKernel\Event\ResponseEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }
        $response = $event->getResponse();
        if (null === $response->getCharset()) {
            $response->setCharset($this->charset);
        }
        $response->prepare($event->getRequest());
    }
    public static function getSubscribedEvents() : array
    {
        return [\ConfigTransformer20210601\Symfony\Component\HttpKernel\KernelEvents::RESPONSE => 'onKernelResponse'];
    }
}
