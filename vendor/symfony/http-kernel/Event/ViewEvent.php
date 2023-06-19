<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpKernel\Event;

use ECSPrefix202306\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202306\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to create a response for the return value of a controller.
 *
 * Call setResponse() to set the response that will be returned for the
 * current request. The propagation of this event is stopped as soon as a
 * response is set.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ViewEvent extends RequestEvent
{
    /**
     * @var mixed
     */
    private $controllerResult;
    /**
     * @param mixed $controllerResult
     */
    public function __construct(HttpKernelInterface $kernel, Request $request, int $requestType, $controllerResult)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->controllerResult = $controllerResult;
    }
    /**
     * @return mixed
     */
    public function getControllerResult()
    {
        return $this->controllerResult;
    }
    /**
     * @param mixed $controllerResult
     */
    public function setControllerResult($controllerResult) : void
    {
        $this->controllerResult = $controllerResult;
    }
}
