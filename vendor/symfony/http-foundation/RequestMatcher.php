<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation;

\ECSPrefix202306\trigger_deprecation('symfony/http-foundation', '6.2', 'The "%s" class is deprecated, use "%s" instead.', RequestMatcher::class, ChainRequestMatcher::class);
/**
 * RequestMatcher compares a pre-defined set of checks against a Request instance.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @deprecated since Symfony 6.2, use ChainRequestMatcher instead
 */
class RequestMatcher implements RequestMatcherInterface
{
    /**
     * @var string|null
     */
    private $path;
    /**
     * @var string|null
     */
    private $host;
    /**
     * @var int|null
     */
    private $port;
    /**
     * @var string[]
     */
    private $methods = [];
    /**
     * @var string[]
     */
    private $ips = [];
    /**
     * @var string[]
     */
    private $attributes = [];
    /**
     * @var string[]
     */
    private $schemes = [];
    /**
     * @param string|mixed[] $methods
     * @param string|mixed[] $ips
     * @param string|mixed[] $schemes
     */
    public function __construct(string $path = null, string $host = null, $methods = null, $ips = null, array $attributes = [], $schemes = null, int $port = null)
    {
        $this->matchPath($path);
        $this->matchHost($host);
        $this->matchMethod($methods);
        $this->matchIps($ips);
        $this->matchScheme($schemes);
        $this->matchPort($port);
        foreach ($attributes as $k => $v) {
            $this->matchAttribute($k, $v);
        }
    }
    /**
     * Adds a check for the HTTP scheme.
     *
     * @param string|string[]|null $scheme An HTTP scheme or an array of HTTP schemes
     *
     * @return void
     */
    public function matchScheme($scheme)
    {
        $this->schemes = null !== $scheme ? \array_map('strtolower', (array) $scheme) : [];
    }
    /**
     * Adds a check for the URL host name.
     *
     * @return void
     */
    public function matchHost(?string $regexp)
    {
        $this->host = $regexp;
    }
    /**
     * Adds a check for the the URL port.
     *
     * @param int|null $port The port number to connect to
     *
     * @return void
     */
    public function matchPort(?int $port)
    {
        $this->port = $port;
    }
    /**
     * Adds a check for the URL path info.
     *
     * @return void
     */
    public function matchPath(?string $regexp)
    {
        $this->path = $regexp;
    }
    /**
     * Adds a check for the client IP.
     *
     * @param string $ip A specific IP address or a range specified using IP/netmask like 192.168.1.0/24
     *
     * @return void
     */
    public function matchIp(string $ip)
    {
        $this->matchIps($ip);
    }
    /**
     * Adds a check for the client IP.
     *
     * @param string|string[]|null $ips A specific IP address or a range specified using IP/netmask like 192.168.1.0/24
     *
     * @return void
     */
    public function matchIps($ips)
    {
        $ips = null !== $ips ? (array) $ips : [];
        $this->ips = \array_reduce($ips, static function (array $ips, string $ip) {
            return \array_merge($ips, \preg_split('/\\s*,\\s*/', $ip));
        }, []);
    }
    /**
     * Adds a check for the HTTP method.
     *
     * @param string|string[]|null $method An HTTP method or an array of HTTP methods
     *
     * @return void
     */
    public function matchMethod($method)
    {
        $this->methods = null !== $method ? \array_map('strtoupper', (array) $method) : [];
    }
    /**
     * Adds a check for request attribute.
     *
     * @return void
     */
    public function matchAttribute(string $key, string $regexp)
    {
        $this->attributes[$key] = $regexp;
    }
    public function matches(Request $request) : bool
    {
        if ($this->schemes && !\in_array($request->getScheme(), $this->schemes, \true)) {
            return \false;
        }
        if ($this->methods && !\in_array($request->getMethod(), $this->methods, \true)) {
            return \false;
        }
        foreach ($this->attributes as $key => $pattern) {
            $requestAttribute = $request->attributes->get($key);
            if (!\is_string($requestAttribute)) {
                return \false;
            }
            if (!\preg_match('{' . $pattern . '}', $requestAttribute)) {
                return \false;
            }
        }
        if (null !== $this->path && !\preg_match('{' . $this->path . '}', \rawurldecode($request->getPathInfo()))) {
            return \false;
        }
        if (null !== $this->host && !\preg_match('{' . $this->host . '}i', $request->getHost())) {
            return \false;
        }
        if (null !== $this->port && 0 < $this->port && $request->getPort() !== $this->port) {
            return \false;
        }
        if (IpUtils::checkIp($request->getClientIp() ?? '', $this->ips)) {
            return \true;
        }
        // Note to future implementors: add additional checks above the
        // foreach above or else your check might not be run!
        return 0 === \count($this->ips);
    }
}
