<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202302\Symfony\Component\ErrorHandler\ErrorRenderer;

use ECSPrefix202302\Symfony\Component\ErrorHandler\Exception\FlattenException;
use ECSPrefix202302\Symfony\Component\HttpFoundation\Request;
use ECSPrefix202302\Symfony\Component\HttpFoundation\RequestStack;
use ECSPrefix202302\Symfony\Component\Serializer\Exception\NotEncodableValueException;
use ECSPrefix202302\Symfony\Component\Serializer\SerializerInterface;
/**
 * Formats an exception using Serializer for rendering.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class SerializerErrorRenderer implements ErrorRendererInterface
{
    /**
     * @var \Symfony\Component\Serializer\SerializerInterface
     */
    private $serializer;
    /**
     * @var string|\Closure
     */
    private $format;
    /**
     * @var \Symfony\Component\ErrorHandler\ErrorRenderer\ErrorRendererInterface
     */
    private $fallbackErrorRenderer;
    /**
     * @var bool|\Closure
     */
    private $debug;
    /**
     * @param string|callable(FlattenException) $format The format as a string or a callable that should return it
     *                                                  formats not supported by Request::getMimeTypes() should be given as mime types
     * @param bool|callable                     $debug  The debugging mode as a boolean or a callable that should return it
     */
    public function __construct(SerializerInterface $serializer, $format, ErrorRendererInterface $fallbackErrorRenderer = null, $debug = \false)
    {
        $this->serializer = $serializer;
        $this->format = \is_string($format) ? $format : \Closure::fromCallable($format);
        $this->fallbackErrorRenderer = $fallbackErrorRenderer ?? new HtmlErrorRenderer();
        $this->debug = \is_bool($debug) ? $debug : \Closure::fromCallable($debug);
    }
    public function render(\Throwable $exception) : FlattenException
    {
        $headers = [];
        $debug = \is_bool($this->debug) ? $this->debug : ($this->debug)($exception);
        if ($debug) {
            $headers['X-Debug-Exception'] = \rawurlencode($exception->getMessage());
            $headers['X-Debug-Exception-File'] = \rawurlencode($exception->getFile()) . ':' . $exception->getLine();
        }
        $flattenException = FlattenException::createFromThrowable($exception, null, $headers);
        try {
            $format = \is_string($this->format) ? $this->format : ($this->format)($flattenException);
            $headers = ['Content-Type' => Request::getMimeTypes($format)[0] ?? $format, 'Vary' => 'Accept'];
            return $flattenException->setAsString($this->serializer->serialize($flattenException, $format, ['exception' => $exception, 'debug' => $debug]))->setHeaders($flattenException->getHeaders() + $headers);
        } catch (NotEncodableValueException $exception2) {
            return $this->fallbackErrorRenderer->render($exception);
        }
    }
    public static function getPreferredFormat(RequestStack $requestStack) : \Closure
    {
        return static function () use($requestStack) {
            if (!($request = $requestStack->getCurrentRequest())) {
                throw new NotEncodableValueException();
            }
            return $request->getPreferredFormat();
        };
    }
}
