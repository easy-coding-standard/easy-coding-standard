<?php

namespace ECSPrefix20210507\Jean85\Exception;

interface VersionMissingExceptionInterface extends \Throwable
{
    /**
     * @return $this
     * @param string $packageName
     */
    public static function create($packageName);
}
