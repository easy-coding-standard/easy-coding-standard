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
 * Allows filtering of a controller callable.
 *
 * You can call getController() to retrieve the current controller. With
 * setController() you can set a new controller that is used in the processing
 * of the request.
 *
 * Controllers should be callables.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ControllerEvent extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $controller;
    /**
     * @param int|null $requestType
     * @param \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Request $request
     */
    public function __construct($kernel, callable $controller, $request, $requestType)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setController($controller);
    }
    /**
     * @return callable
     */
    public function getController()
    {
        return $this->controller;
    }
    /**
     * @return void
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }
}
