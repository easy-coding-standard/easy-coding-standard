<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\EventDispatcher\Event;

use PHP_CodeSniffer\Files\File;
use Symfony\Component\EventDispatcher\Event;

final class CheckFileTokenEvent extends Event
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var int
     */
    private $stackPointer;

    public function __construct(File $file, int $stackPointer)
    {
        $this->file = $file;
        $this->stackPointer = $stackPointer;
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function getStackPointer(): int
    {
        return $this->stackPointer;
    }
}
