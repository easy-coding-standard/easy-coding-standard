<?php

declare (strict_types=1);
namespace ECSPrefix20220220\Symplify\EasyTesting\ValueObject;

use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
final class InputFileInfoAndExpectedFileInfo
{
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $inputFileInfo;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $expectedFileInfo;
    public function __construct(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $inputFileInfo, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $expectedFileInfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFileInfo = $expectedFileInfo;
    }
    public function getInputFileInfo() : \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->inputFileInfo;
    }
    public function getExpectedFileInfo() : \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->expectedFileInfo;
    }
    public function getExpectedFileContent() : string
    {
        return $this->expectedFileInfo->getContents();
    }
    public function getExpectedFileInfoRealPath() : string
    {
        return $this->expectedFileInfo->getRealPath();
    }
}
