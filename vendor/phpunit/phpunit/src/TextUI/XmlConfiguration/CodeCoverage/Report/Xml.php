<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\Report;

use ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Xml
{
    /**
     * @var Directory
     */
    private $target;
    public function __construct(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory $target)
    {
        $this->target = $target;
    }
    public function target() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\Directory
    {
        return $this->target;
    }
}
