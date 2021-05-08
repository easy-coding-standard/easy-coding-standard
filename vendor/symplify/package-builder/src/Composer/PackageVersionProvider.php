<?php

namespace Symplify\PackageBuilder\Composer;

use ECSPrefix20210508\Jean85\Exception\ReplacedPackageException;
use ECSPrefix20210508\Jean85\PrettyVersions;
use ECSPrefix20210508\Jean85\Version;
use OutOfBoundsException;
use ECSPrefix20210508\PharIo\Version\InvalidVersionException;
final class PackageVersionProvider
{
    /**
     * Returns current version of package, contains only major and minor.
     * @param string $packageName
     * @return string
     */
    public function provide($packageName)
    {
        if (\is_object($packageName)) {
            $packageName = (string) $packageName;
        }
        try {
            $version = $this->getVersion($packageName, 'symplify/symplify');
            return $version->getPrettyVersion() ?: 'Unknown';
        } catch (\OutOfBoundsException $exceptoin) {
            return 'Unknown';
        } catch (\ECSPrefix20210508\PharIo\Version\InvalidVersionException $exceptoin) {
            return 'Unknown';
        }
    }
    /**
     * Workaround for when the required package is executed in the monorepo or replaced in any other way
     *
     * @see https://github.com/symplify/symplify/pull/2901#issuecomment-771536136
     * @see https://github.com/Jean85/pretty-package-versions/pull/16#issuecomment-620550459
     * @param string $packageName
     * @param string $replacingPackageName
     * @return \Jean85\Version
     */
    private function getVersion($packageName, $replacingPackageName)
    {
        if (\is_object($replacingPackageName)) {
            $replacingPackageName = (string) $replacingPackageName;
        }
        if (\is_object($packageName)) {
            $packageName = (string) $packageName;
        }
        try {
            return \ECSPrefix20210508\Jean85\PrettyVersions::getVersion($packageName);
        } catch (\OutOfBoundsException $exception) {
            return \ECSPrefix20210508\Jean85\PrettyVersions::getVersion($replacingPackageName);
        } catch (\ECSPrefix20210508\Jean85\Exception\ReplacedPackageException $exception) {
            return \ECSPrefix20210508\Jean85\PrettyVersions::getVersion($replacingPackageName);
        }
    }
}
