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
final class ResponseIsUnprocessable extends Constraint
{
    public function toString() : string
    {
        return 'is unprocessable';
    }
    /**
     * @param Response $other
     */
    protected function matches($other) : bool
    {
        return Response::HTTP_UNPROCESSABLE_ENTITY === $other->getStatusCode();
    }
    /**
     * @param Response $other
     */
    protected function failureDescription($other) : string
    {
        return 'the Response ' . $this->toString();
    }
    /**
     * @param Response $other
     */
    protected function additionalFailureDescription($other) : string
    {
        return (string) $other;
    }
}
