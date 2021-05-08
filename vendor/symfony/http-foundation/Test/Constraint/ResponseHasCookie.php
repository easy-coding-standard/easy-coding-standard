<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\HttpFoundation\Test\Constraint;

use ECSPrefix20210508\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210508\Symfony\Component\HttpFoundation\Cookie;
use ECSPrefix20210508\Symfony\Component\HttpFoundation\Response;
final class ResponseHasCookie extends \ECSPrefix20210508\PHPUnit\Framework\Constraint\Constraint
{
    private $name;
    private $path;
    private $domain;
    /**
     * @param string $name
     * @param string $path
     * @param string $domain
     */
    public function __construct($name, $path = '/', $domain = null)
    {
        $name = (string) $name;
        $path = (string) $path;
        $this->name = $name;
        $this->path = $path;
        $this->domain = $domain;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function toString()
    {
        $str = \sprintf('has cookie "%s"', $this->name);
        if ('/' !== $this->path) {
            $str .= \sprintf(' with path "%s"', $this->path);
        }
        if ($this->domain) {
            $str .= \sprintf(' for domain "%s"', $this->domain);
        }
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
        return null !== $this->getCookie($response);
    }
    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return string
     */
    protected function failureDescription($response)
    {
        return 'the Response ' . $this->toString();
    }
    /**
     * @return \Symfony\Component\HttpFoundation\Cookie|null
     */
    private function getCookie(\ECSPrefix20210508\Symfony\Component\HttpFoundation\Response $response)
    {
        $cookies = $response->headers->getCookies();
        $filteredCookies = \array_filter($cookies, function (\ECSPrefix20210508\Symfony\Component\HttpFoundation\Cookie $cookie) {
            return $cookie->getName() === $this->name && $cookie->getPath() === $this->path && $cookie->getDomain() === $this->domain;
        });
        return \reset($filteredCookies) ?: null;
    }
}
