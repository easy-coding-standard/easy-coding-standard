<?php

namespace Symplify\EasyTesting\ValueObject;

use Symplify\SmartFileSystem\SmartFileInfo;
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
     * @param \Symplify\SmartFileSystem\SmartFileInfo $inputFileInfo
     */
    public function __construct($inputFileInfo, $expected)
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
