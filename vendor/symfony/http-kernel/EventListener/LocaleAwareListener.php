<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210524\Symfony\Component\HttpKernel\EventListener;

use ECSPrefix20210524\Symfony\Component\EventDispatcher\EventSubscriberInterface;
use ECSPrefix20210524\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix20210524\Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use ECSPrefix20210524\Symfony\Component\HttpKernel\Event\RequestEvent;
use ECSPrefix20210524\Symfony\Component\HttpKernel\KernelEvents;
use ECSPrefix20210524\Symfony\Contracts\Translation\LocaleAwareInterface;
/**
 * Pass the current locale to the provided services.
 *
 * @author Pierre Bobiet <pierrebobiet@gmail.com>
 */
class LocaleAwareListener implements \ECSPrefix20210524\Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    private $localeAwareServices;
    private $requestStack;
    /**
     * @param LocaleAwareInterface[] $localeAwareServices
     */
    public function __construct($localeAwareServices, \ECSPrefix20210524\Symfony\Component\HttpFoundation\RequestStack $requestStack)
    {
        $this->localeAwareServices = $localeAwareServices;
        $this->requestStack = $requestStack;
    }
    /**
     * @return void
     */
    public function onKernelRequest(\ECSPrefix20210524\Symfony\Component\HttpKernel\Event\RequestEvent $event)
    {
        $this->setLocale($event->getRequest()->getLocale(), $event->getRequest()->getDefaultLocale());
    }
    /**
     * @return void
     */
    public function onKernelFinishRequest(\ECSPrefix20210524\Symfony\Component\HttpKernel\Event\FinishRequestEvent $event)
    {
        if (null === ($parentRequest = $this->requestStack->getParentRequest())) {
            foreach ($this->localeAwareServices as $service) {
                $service->setLocale($event->getRequest()->getDefaultLocale());
            }
            return;
        }
        $this->setLocale($parentRequest->getLocale(), $parentRequest->getDefaultLocale());
    }
    public static function getSubscribedEvents()
    {
        return [
            // must be registered after the Locale listener
            \ECSPrefix20210524\Symfony\Component\HttpKernel\KernelEvents::REQUEST => [['onKernelRequest', 15]],
            \ECSPrefix20210524\Symfony\Component\HttpKernel\KernelEvents::FINISH_REQUEST => [['onKernelFinishRequest', -15]],
        ];
    }
    /**
     * @return void
     */
    private function setLocale(string $locale, string $defaultLocale)
    {
        foreach ($this->localeAwareServices as $service) {
            try {
                $service->setLocale($locale);
            } catch (\InvalidArgumentException $e) {
                $service->setLocale($defaultLocale);
            }
        }
    }
}
