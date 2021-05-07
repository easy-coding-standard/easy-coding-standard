<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpKernel\Controller;

use ECSPrefix20210507\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface;
use ECSPrefix20210507\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210507\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210507\Symfony\Component\HttpKernel\Exception\HttpException;
use ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Renders error or exception pages from a given FlattenException.
 *
 * @author Yonel Ceruto <yonelceruto@gmail.com>
 * @author Matthias Pigulla <mp@webfactory.de>
 */
class ErrorController
{
    private $kernel;
    private $controller;
    private $errorRenderer;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \ECSPrefix20210507\Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface $errorRenderer
     */
    public function __construct($kernel, $controller, $errorRenderer)
    {
        $this->kernel = $kernel;
        $this->controller = $controller;
        $this->errorRenderer = $errorRenderer;
    }
    /**
     * @param \Throwable $exception
     * @return \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response
     */
    public function __invoke($exception)
    {
        $exception = $this->errorRenderer->render($exception);
        return new \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response($exception->getAsString(), $exception->getStatusCode(), $exception->getHeaders());
    }
    /**
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Request $request
     * @param int $code
     * @return \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response
     */
    public function preview($request, $code)
    {
        /*
         * This Request mimics the parameters set by
         * \Symfony\Component\HttpKernel\EventListener\ErrorListener::duplicateRequest, with
         * the additional "showException" flag.
         */
        $subRequest = $request->duplicate(null, null, ['_controller' => $this->controller, 'exception' => new \ECSPrefix20210507\Symfony\Component\HttpKernel\Exception\HttpException($code, 'This is a sample exception.'), 'logger' => null, 'showException' => \false]);
        return $this->kernel->handle($subRequest, \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface::SUB_REQUEST);
    }
}
