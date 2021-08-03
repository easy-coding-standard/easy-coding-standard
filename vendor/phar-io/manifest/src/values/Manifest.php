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
namespace ECSPrefix20210803\PharIo\Manifest;

use ECSPrefix20210803\PharIo\Version\Version;
class Manifest
{
    /** @var ApplicationName */
    private $name;
    /** @var Version */
    private $version;
    /** @var Type */
    private $type;
    /** @var CopyrightInformation */
    private $copyrightInformation;
    /** @var RequirementCollection */
    private $requirements;
    /** @var BundledComponentCollection */
    private $bundledComponents;
    public function __construct(\ECSPrefix20210803\PharIo\Manifest\ApplicationName $name, \ECSPrefix20210803\PharIo\Version\Version $version, \ECSPrefix20210803\PharIo\Manifest\Type $type, \ECSPrefix20210803\PharIo\Manifest\CopyrightInformation $copyrightInformation, \ECSPrefix20210803\PharIo\Manifest\RequirementCollection $requirements, \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollection $bundledComponents)
    {
        $this->name = $name;
        $this->version = $version;
        $this->type = $type;
        $this->copyrightInformation = $copyrightInformation;
        $this->requirements = $requirements;
        $this->bundledComponents = $bundledComponents;
    }
    public function getName() : \ECSPrefix20210803\PharIo\Manifest\ApplicationName
    {
        return $this->name;
    }
    public function getVersion() : \ECSPrefix20210803\PharIo\Version\Version
    {
        return $this->version;
    }
    public function getType() : \ECSPrefix20210803\PharIo\Manifest\Type
    {
        return $this->type;
    }
    public function getCopyrightInformation() : \ECSPrefix20210803\PharIo\Manifest\CopyrightInformation
    {
        return $this->copyrightInformation;
    }
    public function getRequirements() : \ECSPrefix20210803\PharIo\Manifest\RequirementCollection
    {
        return $this->requirements;
    }
    public function getBundledComponents() : \ECSPrefix20210803\PharIo\Manifest\BundledComponentCollection
    {
        return $this->bundledComponents;
    }
    public function isApplication() : bool
    {
        return $this->type->isApplication();
    }
    public function isLibrary() : bool
    {
        return $this->type->isLibrary();
    }
    public function isExtension() : bool
    {
        return $this->type->isExtension();
    }
    public function isExtensionFor(\ECSPrefix20210803\PharIo\Manifest\ApplicationName $application, \ECSPrefix20210803\PharIo\Version\Version $version = null) : bool
    {
        if (!$this->isExtension()) {
            return \false;
        }
        /** @var Extension $type */
        $type = $this->type;
        if ($version !== null) {
            return $type->isCompatibleWith($application, $version);
        }
        return $type->isExtensionFor($application);
    }
}
