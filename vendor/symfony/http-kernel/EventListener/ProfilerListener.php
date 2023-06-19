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
use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestMatcherInterface;
use ECSPrefix202306\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Session\Session;
use ECSPrefix202306\Symfony\Component\HttpKernel\Event\ExceptionEvent;
use ECSPrefix202306\Symfony\Component\HttpKernel\Event\ResponseEvent;
use ECSPrefix202306\Symfony\Component\HttpKernel\Event\TerminateEvent;
use ECSPrefix202306\Symfony\Component\HttpKernel\KernelEvents;
use ECSPrefix202306\Symfony\Component\HttpKernel\Profiler\Profile;
use ECSPrefix202306\Symfony\Component\HttpKernel\Profiler\Profiler;
/**
 * ProfilerListener collects data for the current request by listening to the kernel events.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class ProfilerListener implements EventSubscriberInterface
{
    /**
     * @var \Symfony\Component\HttpKernel\Profiler\Profiler
     */
    private $profiler;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestMatcherInterface|null
     */
    private $matcher;
    /**
     * @var bool
     */
    private $onlyException;
    /**
     * @var bool
     */
    private $onlyMainRequests;
    /**
     * @var \Throwable|null
     */
    private $exception;
    /** @var \SplObjectStorage<Request, Profile> */
    private $profiles;
    /**
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    /**
     * @var string|null
     */
    private $collectParameter;
    /** @var \SplObjectStorage<Request, Request|null> */
    private $parents;
    /**
     * @param bool $onlyException    True if the profiler only collects data when an exception occurs, false otherwise
     * @param bool $onlyMainRequests True if the profiler only collects data when the request is the main request, false otherwise
     */
    public function __construct(Profiler $profiler, RequestStack $requestStack, RequestMatcherInterface $matcher = null, bool $onlyException = \false, bool $onlyMainRequests = \false, string $collectParameter = null)
    {
        $this->profiler = $profiler;
        $this->matcher = $matcher;
        $this->onlyException = $onlyException;
        $this->onlyMainRequests = $onlyMainRequests;
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
        $this->requestStack = $requestStack;
        $this->collectParameter = $collectParameter;
    }
    /**
     * Handles the onKernelException event.
     */
    public function onKernelException(ExceptionEvent $event)
    {
        if ($this->onlyMainRequests && !$event->isMainRequest()) {
            return;
        }
        $this->exception = $event->getThrowable();
    }
    /**
     * Handles the onKernelResponse event.
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if ($this->onlyMainRequests && !$event->isMainRequest()) {
            return;
        }
        if ($this->onlyException && null === $this->exception) {
            return;
        }
        $request = $event->getRequest();
        if (null !== $this->collectParameter && null !== ($collectParameterValue = $request->get($this->collectParameter))) {
            \true === $collectParameterValue || \filter_var($collectParameterValue, \FILTER_VALIDATE_BOOLEAN) ? $this->profiler->enable() : $this->profiler->disable();
        }
        $exception = $this->exception;
        $this->exception = null;
        if (null !== $this->matcher && !$this->matcher->matches($request)) {
            return;
        }
        $session = $request->hasPreviousSession() && $request->hasSession() ? $request->getSession() : null;
        if ($session instanceof Session) {
            $usageIndexValue = $usageIndexReference =& $session->getUsageIndex();
            $usageIndexReference = \PHP_INT_MIN;
        }
        try {
            if (!($profile = $this->profiler->collect($request, $event->getResponse(), $exception))) {
                return;
            }
        } finally {
            if ($session instanceof Session) {
                $usageIndexReference = $usageIndexValue;
            }
        }
        $this->profiles[$request] = $profile;
        $this->parents[$request] = $this->requestStack->getParentRequest();
    }
    public function onKernelTerminate(TerminateEvent $event)
    {
        // attach children to parents
        foreach ($this->profiles as $request) {
            if (null !== ($parentRequest = $this->parents[$request])) {
                if (isset($this->profiles[$parentRequest])) {
                    $this->profiles[$parentRequest]->addChild($this->profiles[$request]);
                }
            }
        }
        // save profiles
        foreach ($this->profiles as $request) {
            $this->profiler->saveProfile($this->profiles[$request]);
        }
        $this->profiles = new \SplObjectStorage();
        $this->parents = new \SplObjectStorage();
    }
    public static function getSubscribedEvents() : array
    {
        return [KernelEvents::RESPONSE => ['onKernelResponse', -100], KernelEvents::EXCEPTION => ['onKernelException', 0], KernelEvents::TERMINATE => ['onKernelTerminate', -1024]];
    }
}
