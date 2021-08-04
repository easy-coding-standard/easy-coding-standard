<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Manifest.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PharIo\Manifest;

use ECSPrefix20210804\PharIo\Version\VersionConstraint;
abstract class Type
{
    public static function application() : \ECSPrefix20210804\PharIo\Manifest\Application
    {
        return new \ECSPrefix20210804\PharIo\Manifest\Application();
    }
    public static function library() : \ECSPrefix20210804\PharIo\Manifest\Library
    {
        return new \ECSPrefix20210804\PharIo\Manifest\Library();
    }
    public static function extension(\ECSPrefix20210804\PharIo\Manifest\ApplicationName $application, \ECSPrefix20210804\PharIo\Version\VersionConstraint $versionConstraint) : \ECSPrefix20210804\PharIo\Manifest\Extension
    {
        return new \ECSPrefix20210804\PharIo\Manifest\Extension($application, $versionConstraint);
    }
    /** @psalm-assert-if-true Application $this */
    public function isApplication() : bool
    {
        return \false;
    }
    /** @psalm-assert-if-true Library $this */
    public function isLibrary() : bool
    {
        return \false;
    }
    /** @psalm-assert-if-true Extension $this */
    public function isExtension() : bool
    {
        return \false;
    }
}
