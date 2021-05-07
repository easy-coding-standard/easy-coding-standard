<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpFoundation;

use ECSPrefix20210507\Symfony\Component\Routing\RequestContext;
/**
 * A helper service for manipulating URLs within and outside the request scope.
 *
 * @author Valentin Udaltsov <udaltsov.valentin@gmail.com>
 */
final class UrlHelper
{
    private $requestStack;
    private $requestContext;
    /**
     * @param \ECSPrefix20210507\Symfony\Component\HttpFoundation\RequestStack $requestStack
     * @param \ECSPrefix20210507\Symfony\Component\Routing\RequestContext $requestContext
     */
    public function __construct($requestStack, $requestContext = null)
    {
        $this->requestStack = $requestStack;
        $this->requestContext = $requestContext;
    }
    /**
     * @param string $path
     * @return string
     */
    public function getAbsoluteUrl($path)
    {
        if (\false !== \strpos($path, '://') || '//' === \substr($path, 0, 2)) {
            return $path;
        }
        if (null === ($request = $this->requestStack->getMasterRequest())) {
            return $this->getAbsoluteUrlFromContext($path);
        }
        if ('#' === $path[0]) {
            $path = $request->getRequestUri() . $path;
        } elseif ('?' === $path[0]) {
            $path = $request->getPathInfo() . $path;
        }
        if (!$path || '/' !== $path[0]) {
            $prefix = $request->getPathInfo();
            $last = \strlen($prefix) - 1;
            if ($last !== ($pos = \strrpos($prefix, '/'))) {
                $prefix = \substr($prefix, 0, $pos) . '/';
            }
            return $request->getUriForPath($prefix . $path);
        }
        return $request->getSchemeAndHttpHost() . $path;
    }
    /**
     * @param string $path
     * @return string
     */
    public function getRelativePath($path)
    {
        if (\false !== \strpos($path, '://') || '//' === \substr($path, 0, 2)) {
            return $path;
        }
        if (null === ($request = $this->requestStack->getMasterRequest())) {
            return $path;
        }
        return $request->getRelativeUriForPath($path);
    }
    /**
     * @param string $path
     * @return string
     */
    private function getAbsoluteUrlFromContext($path)
    {
        if (null === $this->requestContext || '' === ($host = $this->requestContext->getHost())) {
            return $path;
        }
        $scheme = $this->requestContext->getScheme();
        $port = '';
        if ('http' === $scheme && 80 !== $this->requestContext->getHttpPort()) {
            $port = ':' . $this->requestContext->getHttpPort();
        } elseif ('https' === $scheme && 443 !== $this->requestContext->getHttpsPort()) {
            $port = ':' . $this->requestContext->getHttpsPort();
        }
        if ('#' === $path[0]) {
            $queryString = $this->requestContext->getQueryString();
            $path = $this->requestContext->getPathInfo() . ($queryString ? '?' . $queryString : '') . $path;
        } elseif ('?' === $path[0]) {
            $path = $this->requestContext->getPathInfo() . $path;
        }
        if ('/' !== $path[0]) {
            $path = \rtrim($this->requestContext->getBaseUrl(), '/') . '/' . $path;
        }
        return $scheme . '://' . $host . $port . $path;
    }
}
