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
use Symfony\Component\HttpFoundation\Response;

final class ResponseHasHeader extends Constraint
{
    private $headerName;

    /**
     * @param string $headerName
     */
    public function __construct($headerName)
    {
        $headerName = (string) $headerName;
        $this->headerName = $headerName;
    }

    /**
     * {@inheritdoc}
     * @return string
     */
    public function toString()
    {
        return sprintf('has header "%s"', $this->headerName);
    }

    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return bool
     */
    protected function matches($response)
    {
        return $response->headers->has($this->headerName);
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
}
