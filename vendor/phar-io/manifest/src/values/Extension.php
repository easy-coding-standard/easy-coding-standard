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

use ECSPrefix20210804\PharIo\Version\Version;
use ECSPrefix20210804\PharIo\Version\VersionConstraint;
class Extension extends \ECSPrefix20210804\PharIo\Manifest\Type
{
    /** @var ApplicationName */
    private $application;
    /** @var VersionConstraint */
    private $versionConstraint;
    public function __construct(\ECSPrefix20210804\PharIo\Manifest\ApplicationName $application, \ECSPrefix20210804\PharIo\Version\VersionConstraint $versionConstraint)
    {
        $this->application = $application;
        $this->versionConstraint = $versionConstraint;
    }
    public function getApplicationName() : \ECSPrefix20210804\PharIo\Manifest\ApplicationName
    {
        return $this->application;
    }
    public function getVersionConstraint() : \ECSPrefix20210804\PharIo\Version\VersionConstraint
    {
        return $this->versionConstraint;
    }
    public function isExtension() : bool
    {
        return \true;
    }
    public function isExtensionFor(\ECSPrefix20210804\PharIo\Manifest\ApplicationName $name) : bool
    {
        return $this->application->isEqual($name);
    }
    public function isCompatibleWith(\ECSPrefix20210804\PharIo\Manifest\ApplicationName $name, \ECSPrefix20210804\PharIo\Version\Version $version) : bool
    {
        return $this->isExtensionFor($name) && $this->versionConstraint->complies($version);
    }
}
