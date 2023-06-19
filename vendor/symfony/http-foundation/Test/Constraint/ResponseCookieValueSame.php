<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix202306\Symfony\Component\HttpFoundation\Test\Constraint;

use ECSPrefix202306\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Cookie;
use ECSPrefix202306\Symfony\Component\HttpFoundation\Response;
final class ResponseCookieValueSame extends Constraint
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string|null
     */
    private $domain;
    public function __construct(string $name, string $value, string $path = '/', string $domain = null)
    {
        $this->name = $name;
        $this->value = $value;
        $this->path = $path;
        $this->domain = $domain;
    }
    public function toString() : string
    {
        $str = \sprintf('has cookie "%s"', $this->name);
        if ('/' !== $this->path) {
            $str .= \sprintf(' with path "%s"', $this->path);
        }
        if ($this->domain) {
            $str .= \sprintf(' for domain "%s"', $this->domain);
        }
        $str .= \sprintf(' with value "%s"', $this->value);
        return $str;
    }
    /**
     * @param Response $response
     */
    protected function matches($response) : bool
    {
        $cookie = $this->getCookie($response);
        if (!$cookie) {
            return \false;
        }
        return $this->value === (string) $cookie->getValue();
    }
    /**
     * @param Response $response
     */
    protected function failureDescription($response) : string
    {
        return 'the Response ' . $this->toString();
    }
    protected function getCookie(Response $response) : ?Cookie
    {
        $cookies = $response->headers->getCookies();
        $filteredCookies = \array_filter($cookies, function (Cookie $cookie) {
            return $cookie->getName() === $this->name && $cookie->getPath() === $this->path && $cookie->getDomain() === $this->domain;
        });
        return \reset($filteredCookies) ?: null;
    }
}
