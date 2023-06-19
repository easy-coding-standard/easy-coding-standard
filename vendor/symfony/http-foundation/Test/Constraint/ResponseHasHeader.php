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
use ECSPrefix202306\Symfony\Component\HttpFoundation\Response;
final class ResponseHasHeader extends Constraint
{
    /**
     * @var string
     */
    private $headerName;
    public function __construct(string $headerName)
    {
        $this->headerName = $headerName;
    }
    public function toString() : string
    {
        return \sprintf('has header "%s"', $this->headerName);
    }
    /**
     * @param Response $response
     */
    protected function matches($response) : bool
    {
        return $response->headers->has($this->headerName);
    }
    /**
     * @param Response $response
     */
    protected function failureDescription($response) : string
    {
        return 'the Response ' . $this->toString();
    }
}
