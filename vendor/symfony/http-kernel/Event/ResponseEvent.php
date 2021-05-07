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
use ECSPrefix20210507\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ResponseEvent extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Request $request
     * @param int $requestType
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct($kernel, $request, $requestType, $response)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setResponse($response);
    }
    /**
     * @return \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * @return void
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }
}
