<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211002\Symfony\Component\HttpKernel;

use ECSPrefix20211002\Symfony\Component\HttpClient\HttpClient;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20211002\Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ECSPrefix20211002\Symfony\Component\Mime\Part\AbstractPart;
use ECSPrefix20211002\Symfony\Component\Mime\Part\DataPart;
use ECSPrefix20211002\Symfony\Component\Mime\Part\Multipart\FormDataPart;
use ECSPrefix20211002\Symfony\Component\Mime\Part\TextPart;
use ECSPrefix20211002\Symfony\Contracts\HttpClient\HttpClientInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20211002\Symfony\Component\HttpFoundation\ResponseHeaderBag::class);
/**
 * An implementation of a Symfony HTTP kernel using a "real" HTTP client.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class HttpClientKernel implements \ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface
{
    private $client;
    public function __construct(\ECSPrefix20211002\Symfony\Contracts\HttpClient\HttpClientInterface $client = null)
    {
        if (null === $client && !\class_exists(\ECSPrefix20211002\Symfony\Component\HttpClient\HttpClient::class)) {
            throw new \LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
        }
        $this->client = $client ?? \ECSPrefix20211002\Symfony\Component\HttpClient\HttpClient::create();
    }
    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param int $type
     * @param bool $catch
     */
    public function handle($request, $type = \ECSPrefix20211002\Symfony\Component\HttpKernel\HttpKernelInterface::MAIN_REQUEST, $catch = \true) : \ECSPrefix20211002\Symfony\Component\HttpFoundation\Response
    {
        $headers = $this->getHeaders($request);
        $body = '';
        if (null !== ($part = $this->getBody($request))) {
            $headers = \array_merge($headers, $part->getPreparedHeaders()->toArray());
            $body = $part->bodyToIterable();
        }
        $response = $this->client->request($request->getMethod(), $request->getUri(), ['headers' => $headers, 'body' => $body] + $request->attributes->get('http_client_options', []));
        $response = new \ECSPrefix20211002\Symfony\Component\HttpFoundation\Response($response->getContent(!$catch), $response->getStatusCode(), $response->getHeaders(!$catch));
        $response->headers->remove('X-Body-File');
        $response->headers->remove('X-Body-Eval');
        $response->headers->remove('X-Content-Digest');
        $response->headers = new class($response->headers->all()) extends \ECSPrefix20211002\Symfony\Component\HttpFoundation\ResponseHeaderBag
        {
            protected function computeCacheControlValue() : string
            {
                return $this->getCacheControlHeader();
                // preserve the original value
            }
        };
        return $response;
    }
    private function getBody(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Request $request) : ?\ECSPrefix20211002\Symfony\Component\Mime\Part\AbstractPart
    {
        if (\in_array($request->getMethod(), ['GET', 'HEAD'])) {
            return null;
        }
        if (!\class_exists(\ECSPrefix20211002\Symfony\Component\Mime\Part\AbstractPart::class)) {
            throw new \LogicException('You cannot pass non-empty bodies as the Mime component is not installed. Try running "composer require symfony/mime".');
        }
        if ($content = $request->getContent()) {
            return new \ECSPrefix20211002\Symfony\Component\Mime\Part\TextPart($content, 'utf-8', 'plain', '8bit');
        }
        $fields = $request->request->all();
        foreach ($request->files->all() as $name => $file) {
            $fields[$name] = \ECSPrefix20211002\Symfony\Component\Mime\Part\DataPart::fromPath($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType());
        }
        return new \ECSPrefix20211002\Symfony\Component\Mime\Part\Multipart\FormDataPart($fields);
    }
    private function getHeaders(\ECSPrefix20211002\Symfony\Component\HttpFoundation\Request $request) : array
    {
        $headers = [];
        foreach ($request->headers as $key => $value) {
            $headers[$key] = $value;
        }
        $cookies = [];
        foreach ($request->cookies->all() as $name => $value) {
            $cookies[] = $name . '=' . $value;
        }
        if ($cookies) {
            $headers['cookie'] = \implode('; ', $cookies);
        }
        return $headers;
    }
}
