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
class PhpVersionRequirement implements \ECSPrefix20210804\PharIo\Manifest\Requirement
{
    /** @var VersionConstraint */
    private $versionConstraint;
    public function __construct(\ECSPrefix20210804\PharIo\Version\VersionConstraint $versionConstraint)
    {
        $this->versionConstraint = $versionConstraint;
    }
    public function getVersionConstraint() : \ECSPrefix20210804\PharIo\Version\VersionConstraint
    {
        return $this->versionConstraint;
    }
}
