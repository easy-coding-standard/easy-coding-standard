<?php

namespace ECSPrefix20210517\Symplify\EasyTesting\ValueObject;

use ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo;
final class InputFileInfoAndExpected
{
    /**
     * @var SmartFileInfo
     */
    private $inputFileInfo;
    /**
     * @var mixed
     */
    private $expected;
    /**
     * @param mixed $expected
     */
    public function __construct(\ECSPrefix20210517\Symplify\SmartFileSystem\SmartFileInfo $inputFileInfo, $expected)
    {
        $this->inputFileInfo = $inputFileInfo;
        $this->expected = $expected;
    }
    /**
     * @return string
     */
    public function getInputFileContent()
    {
        return $this->inputFileInfo->getContents();
    }
    /**
     * @return \Symplify\SmartFileSystem\SmartFileInfo
     */
    public function getInputFileInfo()
    {
        return $this->inputFileInfo;
    }
    /**
     * @return string
     */
    public function getInputFileRealPath()
    {
        return $this->inputFileInfo->getRealPath();
    }
    /**
     * @return mixed
     */
    public function getExpected()
    {
        return $this->expected;
    }
}
