<?php

namespace ECSPrefix20210508\Jean85\Exception;

class ReplacedPackageException extends \Exception implements \ECSPrefix20210508\Jean85\Exception\VersionMissingExceptionInterface
{
    /**
     * @param string $packageName
     */
    public static function create($packageName) : \ECSPrefix20210508\Jean85\Exception\VersionMissingExceptionInterface
    {
        if (\is_object($packageName)) {
            $packageName = (string) $packageName;
        }
        return new self('Cannot retrieve a version for package ' . $packageName . ' since it is replaced by some other package');
    }
}
