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
 * Allows to execute logic after a response was sent.
 *
 * Since it's only triggered on master requests, the `getRequestType()` method
 * will always return the value of `HttpKernelInterface::MASTER_REQUEST`.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
final class TerminateEvent extends \ECSPrefix20210507\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface $kernel
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Request $request
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response $response
     */
    public function __construct($kernel, $request, $response)
    {
        parent::__construct($kernel, $request, \ECSPrefix20210507\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST);
        $this->response = $response;
    }
    /**
     * @return \ECSPrefix20210507\Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
