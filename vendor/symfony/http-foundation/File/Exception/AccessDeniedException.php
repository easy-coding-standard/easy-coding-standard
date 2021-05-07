<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\HttpFoundation\File\Exception;

/**
 * Thrown when the access on a file was denied.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AccessDeniedException extends \ECSPrefix20210507\Symfony\Component\HttpFoundation\File\Exception\FileException
{
    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(\sprintf('The file %s could not be accessed', $path));
    }
}
