<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\EasyTesting\ValueObject;

use ECSPrefix202306\Symplify\SmartFileSystem\SmartFileInfo;
final class IncorrectAndMissingSkips
{
    /**
     * @var SmartFileInfo[]
     * @readonly
     */
    private $incorrectSkipFileInfos;
    /**
     * @var SmartFileInfo[]
     * @readonly
     */
    private $missingSkipFileInfos;
    /**
     * @param SmartFileInfo[] $incorrectSkipFileInfos
     * @param SmartFileInfo[] $missingSkipFileInfos
     */
    public function __construct(array $incorrectSkipFileInfos, array $missingSkipFileInfos)
    {
        $this->incorrectSkipFileInfos = $incorrectSkipFileInfos;
        $this->missingSkipFileInfos = $missingSkipFileInfos;
    }
    /**
     * @return SmartFileInfo[]
     */
    public function getIncorrectSkipFileInfos() : array
    {
        return $this->incorrectSkipFileInfos;
    }
    /**
     * @return SmartFileInfo[]
     */
    public function getMissingSkipFileInfos() : array
    {
        return $this->missingSkipFileInfos;
    }
    public function getFileCount() : int
    {
        return \count($this->missingSkipFileInfos) + \count($this->incorrectSkipFileInfos);
    }
}
