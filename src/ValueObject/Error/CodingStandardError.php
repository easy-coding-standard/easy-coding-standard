<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\SmartFileSystem\SmartFileInfo;

final class CodingStandardError
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
     * @var string
     */
    private $checkerClass;

    /**
     * @var SmartFileInfo
     */
    private $fileInfo;

    public function __construct(int $line, string $message, string $checkerClass, SmartFileInfo $fileInfo)
    {
        $this->line = $line;
        $this->message = $message;
        $this->checkerClass = $checkerClass;
        $this->fileInfo = $fileInfo;
    }

    public function getLine(): int
    {
        return $this->line;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getCheckerClass(): string
    {
        return $this->checkerClass;
    }

    public function getFileWithLine(): string
    {
        return $this->getRelativeFilePathFromCwd() . ':' . $this->line;
    }

    public function getRelativeFilePathFromCwd(): string
    {
        return $this->fileInfo->getRelativeFilePathFromCwd();
    }
}
