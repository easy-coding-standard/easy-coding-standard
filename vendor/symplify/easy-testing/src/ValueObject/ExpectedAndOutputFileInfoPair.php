<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\EasyTesting\ValueObject;

use ECSPrefix202306\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix202306\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @api
 */
final class ExpectedAndOutputFileInfoPair
{
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileInfo
     */
    private $expectedFileInfo;
    /**
     * @readonly
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $outputFileInfo;
    public function __construct(SmartFileInfo $expectedFileInfo, ?SmartFileInfo $outputFileInfo)
    {
        $this->expectedFileInfo = $expectedFileInfo;
        $this->outputFileInfo = $outputFileInfo;
    }
    public function getExpectedFileContent() : string
    {
        return $this->expectedFileInfo->getContents();
    }
    public function getOutputFileContent() : string
    {
        if (!$this->outputFileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }
        return $this->outputFileInfo->getContents();
    }
    public function doesOutputFileExist() : bool
    {
        return $this->outputFileInfo !== null;
    }
}
