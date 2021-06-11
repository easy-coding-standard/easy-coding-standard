<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject\Error;

use ECSPrefix20210611\Symplify\SmartFileSystem\SmartFileInfo;
final class SystemError
{
    /**
     * @var int
     */
    private $line;
    /**
     * @var string
     */
    private $message;
    /**
     * @var SmartFileInfo
     */
    private $fileInfo;
    public function __construct(int $line, string $message, \ECSPrefix20210611\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $this->line = $line;
        $this->message = $message;
        $this->fileInfo = $fileInfo;
    }
    public function getMessage() : string
    {
        return $this->message;
    }
    public function getFileWithLine() : string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd() . ':' . $this->line;
    }
}
