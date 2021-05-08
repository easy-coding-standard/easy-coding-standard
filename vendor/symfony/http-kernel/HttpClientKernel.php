<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpKernel;

use ECSPrefix20210508\Symfony\Component\HttpClient\HttpClient;
use ECSPrefix20210508\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210508\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20210508\Symfony\Component\HttpFoundation\ResponseHeaderBag;
use ECSPrefix20210508\Symfony\Component\Mime\Part\AbstractPart;
use ECSPrefix20210508\Symfony\Component\Mime\Part\DataPart;
use ECSPrefix20210508\Symfony\Component\Mime\Part\Multipart\FormDataPart;
use ECSPrefix20210508\Symfony\Component\Mime\Part\TextPart;
use ECSPrefix20210508\Symfony\Contracts\HttpClient\HttpClientInterface;
// Help opcache.preload discover always-needed symbols
\class_exists(\ECSPrefix20210508\Symfony\Component\HttpFoundation\ResponseHeaderBag::class);
/**
 * An implementation of a Symfony HTTP kernel using a "real" HTTP client.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
final class HttpClientKernel implements \ECSPrefix20210508\Symfony\Component\HttpKernel\HttpKernelInterface
{
    private $client;
    public function __construct(\ECSPrefix20210508\Symfony\Contracts\HttpClient\HttpClientInterface $client = null)
    {
        if (null === $client && !\class_exists(\ECSPrefix20210508\Symfony\Component\HttpClient\HttpClient::class)) {
            throw new \LogicException(\sprintf('You cannot use "%s" as the HttpClient component is not installed. Try running "composer require symfony/http-client".', __CLASS__));
        }
        $this->client = isset($client) ? $client : \ECSPrefix20210508\Symfony\Component\HttpClient\HttpClient::create();
    }
    /**
     * @param int $type
     * @param bool $catch
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(\ECSPrefix20210508\Symfony\Component\HttpFoundation\Request $request, $type = \ECSPrefix20210508\Symfony\Component\HttpKernel\HttpKernelInterface::MASTER_REQUEST, $catch = \true)
    {
        $type = (int) $type;
        $catch = (bool) $catch;
        $headers = $this->getHeaders($request);
        $body = '';
        if (null !== ($part = $this->getBody($request))) {
            $headers = \array_merge($headers, $part->getPreparedHeaders()->toArray());
            $body = $part->bodyToIterable();
        }
        $response = $this->client->request($request->getMethod(), $request->getUri(), ['headers' => $headers, 'body' => $body] + $request->attributes->get('http_client_options', []));
        $response = new \ECSPrefix20210508\Symfony\Component\HttpFoundation\Response($response->getContent(!$catch), $response->getStatusCode(), $response->getHeaders(!$catch));
        $response->headers->remove('X-Body-File');
        $response->headers->remove('X-Body-Eval');
        $response->headers->remove('X-Content-Digest');
        $response->headers = new \ECSPrefix20210508\Symfony\Component\HttpKernel\Anonymous__6e0937e1ca473be937c05584b1c8bc14__0($response->headers->all());
        return $response;
    }
    /**
     * @return \Symfony\Component\Mime\Part\AbstractPart|null
     */
    private function getBody(\ECSPrefix20210508\Symfony\Component\HttpFoundation\Request $request)
    {
        if (\in_array($request->getMethod(), ['GET', 'HEAD'])) {
            return null;
        }
        if (!\class_exists(\ECSPrefix20210508\Symfony\Component\Mime\Part\AbstractPart::class)) {
            throw new \LogicException('You cannot pass non-empty bodies as the Mime component is not installed. Try running "composer require symfony/mime".');
        }
        if ($content = $request->getContent()) {
            return new \ECSPrefix20210508\Symfony\Component\Mime\Part\TextPart($content, 'utf-8', 'plain', '8bit');
        }
        $fields = $request->request->all();
        foreach ($request->files->all() as $name => $file) {
            $fields[$name] = \ECSPrefix20210508\Symfony\Component\Mime\Part\DataPart::fromPath($file->getPathname(), $file->getClientOriginalName(), $file->getClientMimeType());
        }
        return new \ECSPrefix20210508\Symfony\Component\Mime\Part\Multipart\FormDataPart($fields);
    }
    /**
     * @return mixed[]
     */
    private function getHeaders(\ECSPrefix20210508\Symfony\Component\HttpFoundation\Request $request)
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
class Anonymous__6e0937e1ca473be937c05584b1c8bc14__0 extends \ECSPrefix20210508\Symfony\Component\HttpFoundation\ResponseHeaderBag
{
    protected function computeCacheControlValue() : string
    {
        return $this->getCacheControlHeader();
        // preserve the original value
    }
}
