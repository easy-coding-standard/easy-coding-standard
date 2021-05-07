<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Config\Exception;

/**
 * Exception class for when a circular reference is detected when importing resources.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class FileLoaderImportCircularReferenceException extends \ECSPrefix20210507\Symfony\Component\Config\Exception\LoaderLoadException
{
    /**
     * @param int|null $code
     * @param \Throwable $previous
     */
    public function __construct(array $resources, $code = 0, $previous = null)
    {
        $message = \sprintf('Circular reference detected in "%s" ("%s" > "%s").', $this->varToString($resources[0]), \implode('" > "', $resources), $resources[0]);
        \Exception::__construct($message, $code, $previous);
    }
}
