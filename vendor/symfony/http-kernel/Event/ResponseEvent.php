<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210703\Symfony\Component\HttpKernel\Event;

use ECSPrefix20210703\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210703\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210703\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ResponseEvent extends \ECSPrefix20210703\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    public function __construct(\ECSPrefix20210703\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ECSPrefix20210703\Symfony\Component\HttpFoundation\Request $request, int $requestType, \ECSPrefix20210703\Symfony\Component\HttpFoundation\Response $response)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setResponse($response);
    }
    public function getResponse() : \ECSPrefix20210703\Symfony\Component\HttpFoundation\Response
    {
        return $this->response;
    }
    /**
     * @return void
     */
    public function setResponse(\ECSPrefix20210703\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->response = $response;
    }
}
