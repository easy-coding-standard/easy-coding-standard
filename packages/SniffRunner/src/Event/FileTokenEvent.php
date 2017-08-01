<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Event;

use PHP_CodeSniffer\Files\File;
use SplFileInfo;

final class FileTokenEvent
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $position;

    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    public function __construct(File $file, int $position, SplFileInfo $fileInfo)
    {
        $this->file = $file;
        $this->position = $position;
        $this->fileInfo = $fileInfo;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getFileInfo(): SplFileInfo
    {
        return $this->fileInfo;
    }
}
