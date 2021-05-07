<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpKernel\Event;

use ECSPrefix20210507\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows filtering of controller arguments.
 *
 * You can call getController() to retrieve the controller and getArguments
 * to retrieve the current arguments. With setArguments() you can replace
 * arguments that are used to call the controller.
 *
 * Arguments set in the event must be compatible with the signature of the
 * controller.
 *
 * @author Christophe Coevoet <stof@notk.org>
 */
final class ControllerArgumentsEvent extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $controller;
    private $arguments;
    /**
     * @param int|null $requestType
     * @param \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct($kernel, callable $controller, array $arguments, $request, $requestType)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->controller = $controller;
        $this->arguments = $arguments;
    }
    /**
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }
    /**
     * @return mixed[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }
}
