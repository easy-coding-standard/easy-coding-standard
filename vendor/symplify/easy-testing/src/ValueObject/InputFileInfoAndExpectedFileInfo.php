<?php

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;
final class InputFileInfoAndExpectedFileInfo
{
    /**
     * @var SmartFileInfo
     */
    private $inputFileInfo;
    /**
     * @var SmartFileInfo
     */
    private $expectedFileInfo;
    public function __construct(\Symplify\SmartFileSystem\SmartFileInfo $inputFileInfo, \Symplify\SmartFileSystem\SmartFileInfo $expectedFileInfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFileInfo = $expectedFileInfo;
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function getInputFileInfo()
    {
        return $this->inputFileInfo;
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function getExpectedFileInfo()
    {
        return $this->expectedFileInfo;
    }
    /**
     * @return string
     */
    public function getExpectedFileContent()
    {
        return $this->expectedFileInfo->getContents();
    }
    /**
     * @return string
     */
    public function getExpectedFileInfoRealPath()
    {
        return $this->expectedFileInfo->getRealPath();
    }
}
