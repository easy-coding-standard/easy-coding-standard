<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\VarExporter\Exception;

class NotInstantiableTypeException extends \Exception implements \ECSPrefix20210507\Symfony\Component\VarExporter\Exception\ExceptionInterface
{
    /**
     * @param string $type
     * @param \Throwable $previous
     */
    public function __construct($type, $previous = null)
    {
        parent::__construct(\sprintf('Type "%s" is not instantiable.', $type), 0, $previous);
    }
}
