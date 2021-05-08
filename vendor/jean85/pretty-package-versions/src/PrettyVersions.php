<?php

namespace ECSPrefix20210508\Jean85;

use Composer\InstalledVersions;
use ECSPrefix20210508\Jean85\Exception\ProvidedPackageException;
use ECSPrefix20210508\Jean85\Exception\ReplacedPackageException;
use ECSPrefix20210508\Jean85\Exception\VersionMissingExceptionInterface;
class PrettyVersions
{
    /**
     * @throws VersionMissingExceptionInterface When a package is provided ({@see ProvidedPackageException}) or replaced ({@see ReplacedPackageException})
     * @param string $packageName
     * @return \Jean85\Version
     */
    public static function getVersion($packageName)
    {
        if (isset(\Composer\InstalledVersions::getRawData()['versions'][$packageName]['provided'])) {
            throw \ECSPrefix20210508\Jean85\Exception\ProvidedPackageException::create($packageName);
        }
        if (isset(\Composer\InstalledVersions::getRawData()['versions'][$packageName]['replaced'])) {
            throw \ECSPrefix20210508\Jean85\Exception\ReplacedPackageException::create($packageName);
        }
        return new \ECSPrefix20210508\Jean85\Version($packageName, \Composer\InstalledVersions::getPrettyVersion($packageName), \Composer\InstalledVersions::getReference($packageName));
    }
    /**
     * @return string
     */
    public static function getRootPackageName()
    {
        return \Composer\InstalledVersions::getRootPackage()['name'];
    }
    /**
     * @return \Jean85\Version
     */
    public static function getRootPackageVersion()
    {
        return new \ECSPrefix20210508\Jean85\Version(self::getRootPackageName(), \Composer\InstalledVersions::getRootPackage()['pretty_version'], \Composer\InstalledVersions::getRootPackage()['reference']);
    }
}
