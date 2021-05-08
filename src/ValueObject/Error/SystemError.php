<?php

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;
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
    /**
     * @param int $line
     * @param string $message
     */
    public function __construct($line, $message, \Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        if (\is_object($message)) {
            $message = (string) $message;
        }
        $this->line = $line;
        $this->message = $message;
        $this->fileInfo = $fileInfo;
    }
    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
    /**
     * @return string
     */
    public function getFileWithLine()
    {
        return $this->fileInfo->getRelativeFilePathFromCwd() . ':' . $this->line;
    }
}
