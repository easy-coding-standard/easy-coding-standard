<?php

namespace ECSPrefix20210507\Jean85\Exception;

class ReplacedPackageException extends \Exception implements \ECSPrefix20210507\Jean85\Exception\VersionMissingExceptionInterface
{
    /**
     * @param string $packageName
     * @return \ECSPrefix20210507\Jean85\Exception\VersionMissingExceptionInterface
     */
    public static function create($packageName)
    {
        return new self('Cannot retrieve a version for package ' . $packageName . ' since it is replaced by some other package');
    }
}
