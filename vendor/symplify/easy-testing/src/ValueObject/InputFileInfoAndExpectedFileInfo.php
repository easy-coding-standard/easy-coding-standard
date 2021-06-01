<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\Symplify\EasyTesting\ValueObject;

use ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo;
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
    public function __construct(\ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $inputFileInfo, \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo $expectedFileInfo)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expectedFileInfo = $expectedFileInfo;
    }
    public function getInputFileInfo() : \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->inputFileInfo;
    }
    public function getExpectedFileInfo() : \ConfigTransformer20210601\Symplify\SmartFileSystem\SmartFileInfo
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
