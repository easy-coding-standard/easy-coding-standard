<?php

declare (strict_types=1);
/*
 * This file is part of PharIo\Version.
 *
 * (c) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PharIo\Version;

class SpecificMajorAndMinorVersionConstraint extends \ECSPrefix20210804\PharIo\Version\AbstractVersionConstraint
{
    /** @var int */
    private $major;
    /** @var int */
    private $minor;
    public function __construct(string $originalValue, int $major, int $minor)
    {
        parent::__construct($originalValue);
        $this->major = $major;
        $this->minor = $minor;
    }
    public function complies(\ECSPrefix20210804\PharIo\Version\Version $version) : bool
    {
        if ($version->getMajor()->getValue() !== $this->major) {
            return \false;
        }
        return $version->getMinor()->getValue() === $this->minor;
    }
}
