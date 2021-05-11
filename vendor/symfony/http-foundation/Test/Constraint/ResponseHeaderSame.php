<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\HttpFoundation\Test\Constraint;

use ECSPrefix20210511\PHPUnit\Framework\Constraint\Constraint;
use ECSPrefix20210511\Symfony\Component\HttpFoundation\Response;
final class ResponseHeaderSame extends \ECSPrefix20210511\PHPUnit\Framework\Constraint\Constraint
{
    private $headerName;
    private $expectedValue;
    /**
     * @param string $headerName
     * @param string $expectedValue
     */
    public function __construct($headerName, $expectedValue)
    {
        $headerName = (string) $headerName;
        $expectedValue = (string) $expectedValue;
        $this->headerName = $headerName;
        $this->expectedValue = $expectedValue;
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function toString()
    {
        return \sprintf('has header "%s" with value "%s"', $this->headerName, $this->expectedValue);
    }
    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return bool
     */
    protected function matches($response)
    {
        return $this->expectedValue === $response->headers->get($this->headerName, null);
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
}
