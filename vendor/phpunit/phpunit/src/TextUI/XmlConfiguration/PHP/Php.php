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
final class Php
{
    /**
     * @var DirectoryCollection
     */
    private $includePaths;
    /**
     * @var IniSettingCollection
     */
    private $iniSettings;
    /**
     * @var ConstantCollection
     */
    private $constants;
    /**
     * @var VariableCollection
     */
    private $globalVariables;
    /**
     * @var VariableCollection
     */
    private $envVariables;
    /**
     * @var VariableCollection
     */
    private $postVariables;
    /**
     * @var VariableCollection
     */
    private $getVariables;
    /**
     * @var VariableCollection
     */
    private $cookieVariables;
    /**
     * @var VariableCollection
     */
    private $serverVariables;
    /**
     * @var VariableCollection
     */
    private $filesVariables;
    /**
     * @var VariableCollection
     */
    private $requestVariables;
    public function __construct(\ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\DirectoryCollection $includePaths, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\IniSettingCollection $iniSettings, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\ConstantCollection $constants, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $globalVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $envVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $postVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $getVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $cookieVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $serverVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $filesVariables, \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection $requestVariables)
    {
        $this->includePaths = $includePaths;
        $this->iniSettings = $iniSettings;
        $this->constants = $constants;
        $this->globalVariables = $globalVariables;
        $this->envVariables = $envVariables;
        $this->postVariables = $postVariables;
        $this->getVariables = $getVariables;
        $this->cookieVariables = $cookieVariables;
        $this->serverVariables = $serverVariables;
        $this->filesVariables = $filesVariables;
        $this->requestVariables = $requestVariables;
    }
    public function includePaths() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\DirectoryCollection
    {
        return $this->includePaths;
    }
    public function iniSettings() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\IniSettingCollection
    {
        return $this->iniSettings;
    }
    public function constants() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\ConstantCollection
    {
        return $this->constants;
    }
    public function globalVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->globalVariables;
    }
    public function envVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->envVariables;
    }
    public function postVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->postVariables;
    }
    public function getVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->getVariables;
    }
    public function cookieVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->cookieVariables;
    }
    public function serverVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->serverVariables;
    }
    public function filesVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->filesVariables;
    }
    public function requestVariables() : \ECSPrefix20210804\PHPUnit\TextUI\XmlConfiguration\VariableCollection
    {
        return $this->requestVariables;
    }
}
