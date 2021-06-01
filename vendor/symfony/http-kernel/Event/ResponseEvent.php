<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\HttpKernel\Event;

use ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request;
use ConfigTransformer20210601\Symfony\Component\HttpFoundation\Response;
use ConfigTransformer20210601\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows to filter a Response object.
 *
 * You can call getResponse() to retrieve the current response. With
 * setResponse() you can set a new response that will be returned to the
 * browser.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ResponseEvent extends \ConfigTransformer20210601\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $response;
    public function __construct(\ConfigTransformer20210601\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Request $request, int $requestType, \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Response $response)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setResponse($response);
    }
    public function getResponse() : \ConfigTransformer20210601\Symfony\Component\HttpFoundation\Response
    {
        return $this->response;
    }
    /**
     * @return void
     */
    public function setResponse(\ConfigTransformer20210601\Symfony\Component\HttpFoundation\Response $response)
    {
        $this->response = $response;
    }
}
