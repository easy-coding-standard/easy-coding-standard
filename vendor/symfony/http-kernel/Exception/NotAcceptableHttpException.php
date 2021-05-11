<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210511\Symfony\Component\HttpKernel\Exception;

/**
 * @author Ben Ramsey <ben@benramsey.com>
 */
class NotAcceptableHttpException extends \ECSPrefix20210511\Symfony\Component\HttpKernel\Exception\HttpException
{
    /**
     * @param string|null     $message  The internal exception message
     * @param \Throwable|null $previous The previous exception
     * @param int             $code     The internal exception code
     */
    public function __construct($message = '', \Throwable $previous = null, $code = 0, array $headers = [])
    {
        $code = (int) $code;
        parent::__construct(406, $message, $previous, $headers, $code);
    }
}
