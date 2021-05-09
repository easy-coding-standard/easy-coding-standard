<?php

namespace ECSPrefix20210509\Jean85;

use ECSPrefix20210509\Composer\InstalledVersions;
use ECSPrefix20210509\Jean85\Exception\ProvidedPackageException;
use ECSPrefix20210509\Jean85\Exception\ReplacedPackageException;
use ECSPrefix20210509\Jean85\Exception\VersionMissingExceptionInterface;
class PrettyVersions
{
    /**
     * @throws VersionMissingExceptionInterface When a package is provided ({@see ProvidedPackageException}) or replaced ({@see ReplacedPackageException})
     * @param string $packageName
     * @return \Jean85\Version
     */
    public static function getVersion($packageName)
    {
        $packageName = (string) $packageName;
        if (isset(\ECSPrefix20210509\Composer\InstalledVersions::getRawData()['versions'][$packageName]['provided'])) {
            throw \ECSPrefix20210509\Jean85\Exception\ProvidedPackageException::create($packageName);
        }
        if (isset(\ECSPrefix20210509\Composer\InstalledVersions::getRawData()['versions'][$packageName]['replaced'])) {
            throw \ECSPrefix20210509\Jean85\Exception\ReplacedPackageException::create($packageName);
        }
        return new \ECSPrefix20210509\Jean85\Version($packageName, \ECSPrefix20210509\Composer\InstalledVersions::getPrettyVersion($packageName), \ECSPrefix20210509\Composer\InstalledVersions::getReference($packageName));
    }
    /**
     * @return string
     */
    public static function getRootPackageName()
    {
        return \ECSPrefix20210509\Composer\InstalledVersions::getRootPackage()['name'];
    }
    /**
     * @return \Jean85\Version
     */
    public static function getRootPackageVersion()
    {
        return new \ECSPrefix20210509\Jean85\Version(self::getRootPackageName(), \ECSPrefix20210509\Composer\InstalledVersions::getRootPackage()['pretty_version'], \ECSPrefix20210509\Composer\InstalledVersions::getRootPackage()['reference']);
    }
}
