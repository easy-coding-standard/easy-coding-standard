<?php

namespace Symplify\SetConfigResolver\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;
final class Set
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var SmartFileInfo
     */
    private $setFileInfo;
    /**
     * @param string $name
     */
    public function __construct($name, \Symplify\SmartFileSystem\SmartFileInfo $setFileInfo)
    {
        $this->name = $name;
        $this->setFileInfo = $setFileInfo;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function getSetFileInfo()
    {
        return $this->setFileInfo;
    }
    /**
     * @return string
     */
    public function getSetPathname()
    {
        return $this->setFileInfo->getPathname();
    }
}
