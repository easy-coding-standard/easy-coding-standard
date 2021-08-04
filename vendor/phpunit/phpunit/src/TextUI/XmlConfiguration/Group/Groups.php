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
namespace ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration;

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 * @psalm-immutable
 */
final class Groups
{
    /**
     * @var GroupCollection
     */
    private $include;
    /**
     * @var GroupCollection
     */
    private $exclude;
    public function __construct(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\GroupCollection $include, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\GroupCollection $exclude)
    {
        $this->include = $include;
        $this->exclude = $exclude;
    }
    public function hasInclude() : bool
    {
        return !$this->include->isEmpty();
    }
    public function include() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\GroupCollection
    {
        return $this->include;
    }
    public function hasExclude() : bool
    {
        return !$this->exclude->isEmpty();
    }
    public function exclude() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\GroupCollection
    {
        return $this->exclude;
    }
}
