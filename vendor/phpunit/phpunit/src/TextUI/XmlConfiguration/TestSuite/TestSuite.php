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
final class TestSuite
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var TestDirectoryCollection
     */
    private $directories;
    /**
     * @var TestFileCollection
     */
    private $files;
    /**
     * @var FileCollection
     */
    private $exclude;
    public function __construct(string $name, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestDirectoryCollection $directories, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestFileCollection $files, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection $exclude)
    {
        $this->name = $name;
        $this->directories = $directories;
        $this->files = $files;
        $this->exclude = $exclude;
    }
    public function name() : string
    {
        return $this->name;
    }
    public function directories() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestDirectoryCollection
    {
        return $this->directories;
    }
    public function files() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\TestFileCollection
    {
        return $this->files;
    }
    public function exclude() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\FileCollection
    {
        return $this->exclude;
    }
}
