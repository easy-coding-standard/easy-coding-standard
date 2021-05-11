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
final class ResponseIsSuccessful extends \ECSPrefix20210511\PHPUnit\Framework\Constraint\Constraint
{
    /**
     * {@inheritdoc}
     * @return string
     */
    public function toString()
    {
        return 'is successful';
    }
    /**
     * @param Response $response
     *
     * {@inheritdoc}
     * @return bool
     */
    protected function matches($response)
    {
        return $response->isSuccessful();
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
     * @param Response $response
     *
     * {@inheritdoc}
     * @return string
     */
    protected function additionalFailureDescription($response)
    {
        return (string) $response;
    }
}
