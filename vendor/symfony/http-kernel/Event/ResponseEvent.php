<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpKernel\Event;

use ECSPrefix20211002\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ResponseEvent extends \ECSPrefix20211002\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    public function __construct(\ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ECSPrefix20211002\Symfony\Component\HttpFoundation\Request $request, int $requestType, \ECSPrefix20211002\Symfony\Component\HttpFoundation\Response $response)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setResponse($response);
    }
    public function getResponse() : \ECSPrefix20211002\Symfony\Component\HttpFoundation\Response
    {
        return $this->response;
    }
    /**
     * @param \Symfony\Component\HttpFoundation\Response $response
     */
    public function setResponse($response) : void
    {
        $this->response = $response;
    }
}
