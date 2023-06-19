<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\EasyTesting\ValueObject;

use ECSPrefix202306\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @api
 */
final class InputFileInfoAndExpectedFileInfo
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $inputFileInfo;
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $expectedFileInfo;
    public function __construct(SmartFileInfo $inputFileInfo, SmartFileInfo $expectedFileInfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFileInfo = $expectedFileInfo;
    }
    public function getInputFileInfo() : SmartFileInfo
    {
        return $this->inputFileInfo;
    }
    public function getExpectedFileInfo() : SmartFileInfo
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
