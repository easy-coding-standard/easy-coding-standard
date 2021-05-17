<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\SetConfigResolver\ValueObject;

use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(string $name, \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo $setFileInfo)
    {
        $this->name = $name;
        $this->setFileInfo = $setFileInfo;
    }
    public function getName() : string
    {
        return $this->name;
    }
    public function getSetFileInfo() : \ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->setFileInfo;
    }
    public function getSetPathname() : string
    {
        return $this->setFileInfo->getPathname();
    }
}
