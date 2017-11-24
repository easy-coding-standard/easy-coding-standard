<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Error;

final class Error
{
    /**
     * @var int|null
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
     * @var bool
     */
    private $isFixable;

    private function __construct(?int $line, string $message, string $sourceClass, bool $isFixable)
    {
        $this->line = $line;
        $this->message = $message;
        $this->sourceClass = $sourceClass;
        $this->isFixable = $isFixable;
    }

    public static function createFromLineMessageSourceClassAndFixable(
        ?int $line,
        string $message,
        string $sourceClass,
        bool $isFixable
    ): self {
        return new self($line, $message, $sourceClass, $isFixable);
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

    public function isFixable(): bool
    {
        return $this->isFixable;
    }
}
