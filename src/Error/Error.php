<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class Error
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
    private $sourceClass;

    /**
     * @var SmartFileInfo
     */
    private $fileInfo;

    public function __construct(int $line, string $message, string $sourceClass, SmartFileInfo $fileInfo)
    {
        $this->line = $line;
        $this->message = $message;
        $this->sourceClass = $sourceClass;
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

    public function getSourceClass(): string
    {
        return $this->sourceClass;
    }

    public function getFileInfo(): SmartFileInfo
    {
        return $this->fileInfo;
    }
}
