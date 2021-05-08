<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Filesystem\Exception;

/**
 * Exception class thrown when a filesystem operation failure happens.
 *
 * @author Romain Neutron <imprec@gmail.com>
 * @author Christian Gärtner <christiangaertner.film@googlemail.com>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class IOException extends \RuntimeException implements \ECSPrefix20210508\Symfony\Component\Filesystem\Exception\IOExceptionInterface
{
    private $path;
    /**
     * @param string $message
     */
    public function __construct($message, int $code = 0, \Throwable $previous = null, string $path = null)
    {
        if (\is_object($message)) {
            $message = (string) $message;
        }
        $this->path = $path;
        parent::__construct($message, $code, $previous);
    }
    /**
     * {@inheritdoc}
     */
    public function getPath()
    {
        return $this->path;
    }
}
