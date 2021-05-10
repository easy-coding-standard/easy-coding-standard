<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\HttpKernel\Event;

use ECSPrefix20210510\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210510\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210510\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ResponseEvent extends \ECSPrefix20210510\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    /**
     * @param int $requestType
     */
    public function __construct(\ECSPrefix20210510\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ECSPrefix20210510\Symfony\Component\HttpFoundation\Request $request, $requestType, \ECSPrefix20210510\Symfony\Component\HttpFoundation\Response $response)
    {
        $requestType = (int) $requestType;
        parent::__construct($kernel, $request, $requestType);
        $this->setResponse($response);
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
    /**
     * @return void
     */
    public function setResponse(\ECSPrefix20210510\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->response = $response;
    }
}
