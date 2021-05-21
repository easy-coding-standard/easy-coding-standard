<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210521\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix20210521\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix20210521\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210521\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix20210521\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use ECSPrefix20210521\Symfony\Component\HttpKernel\Event\KernelEvent;
use ECSPrefix20210521\Symfony\Component\HttpKernel\Event\RequestEvent;
use ECSPrefix20210521\Symfony\Component\HttpKernel\KernelEvents;
use ECSPrefix20210521\Symfony\Component\Routing\RequestContextAwareInterface;
/**
 * Initializes the locale based on the current request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class LocaleListener implements \ECSPrefix20210521\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $router;
    private $defaultLocale;
    private $requestStack;
    public function __construct(\ECSPrefix20210521\Symfony\Component\HttpFoundation\RequestStack $requestStack, string $defaultLocale = 'en', \ECSPrefix20210521\Symfony\Component\Routing\RequestContextAwareInterface $router = null)
    {
        $this->defaultLocale = $defaultLocale;
        $this->requestStack = $requestStack;
        $this->router = $router;
    }
    public function setDefaultLocale(\ECSPrefix20210521\Symfony\Component\HttpKernel\Event\KernelEvent $event)
    {
        $event->getRequest()->setDefaultLocale($this->defaultLocale);
    }
    public function onKernelRequest(\ECSPrefix20210521\Symfony\Component\HttpKernel\Event\RequestEvent $event)
    {
        $request = $event->getRequest();
        $this->setLocale($request);
        $this->setRouterContext($request);
    }
    public function onKernelFinishRequest(\ECSPrefix20210521\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        if (null !== ($parentRequest = $this->requestStack->getParentRequest())) {
            $this->setRouterContext($parentRequest);
        }
    }
    private function setLocale(\ECSPrefix20210521\Symfony\Component\HttpFoundation\Request $request)
    {
        if ($locale = $request->attributes->get('_locale')) {
            $request->setLocale($locale);
        }
    }
    private function setRouterContext(\ECSPrefix20210521\Symfony\Component\HttpFoundation\Request $request)
    {
        if (null !== $this->router) {
            $this->router->getContext()->setParameter('_locale', $request->getLocale());
        }
    }
    public static function getSubscribedEvents() : array
    {
        return [\ECSPrefix20210521\Symfony\Component\HttpKernel\KernelEvents::REQUEST => [
            ['setDefaultLocale', 100],
            // must be registered after the Router to have access to the _locale
            ['onKernelRequest', 16],
        ], \ECSPrefix20210521\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', 0]]];
    }
}
