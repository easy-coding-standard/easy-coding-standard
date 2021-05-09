<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Test\Constraint;

use PHPUnit\Framework\Constraint\Constraint;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

final class ResponseCookieValueSame extends Constraint
{
    private $name;
    private $value;
    private $path;
    private $domain;

    /**
     * @param string $name
     * @param string $value
     * @param string $path
     * @param string $domain
     */
    public function __construct($name, $value, $path = '/', $domain = null)
    {
        $name = (string) $name;
        $value = (string) $value;
        $path = (string) $path;
        $this->name = $name;
        $this->value = $value;
        $this->path = $path;
        $this->domain = $domain;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function toString()
    {
        $str = sprintf('has cookie "%s"', $this->name);
        if ('/' !== $this->path) {
            $str .= sprintf(' with path "%s"', $this->path);
        }
        if ($this->domain) {
            $str .= sprintf(' for domain "%s"', $this->domain);
        }
        $str .= sprintf(' with value "%s"', $this->value);

        return $str;
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return bool
     */
    protected function matches($response)
    {
        $cookie = $this->getCookie($response);
        if (!$cookie) {
            return false;
        }

        return $this->value === $cookie->getValue();
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return string
     */
    protected function failureDescription($response)
    {
        return 'the Response '.$this->toString();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Cookie|null
     */
    protected function getCookie(Response $response)
    {
        $cookies = $response->headers->getCookies();

        $filteredCookies = array_filter($cookies, function (Cookie $cookie) {
            return $cookie->getName() === $this->name && $cookie->getPath() === $this->path && $cookie->getDomain() === $this->domain;
        });

        return reset($filteredCookies) ?: null;
    }
}
