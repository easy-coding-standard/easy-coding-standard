<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Report\Error;

final class Error
{
    /**
     * @var string
     */
    public const LINE = 'line';

    /**
     * @var string
     */
    public const MESSAGE = 'message';

    /**
     * @var string
     */
    public const SOURCE_CLASS = 'sourceClass';

    /**
     * @var string
     */
    public const IS_FIXABLE = 'isFixable';

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
     * @var bool
     */
    private $isFixable;

    public function __construct(int $line, string $message, string $sourceClass, bool $isFixable)
    {
        $this->line = $line;
        $this->message = $message;
        $this->sourceClass = $sourceClass;
        $this->isFixable = $isFixable;
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
