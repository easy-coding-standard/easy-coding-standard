<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;
use Symplify\EasyParallel\Contract\SerializableInterface;

final readonly class SystemError implements SerializableInterface
{
    public function __construct(
        private int $line,
        private string $message,
        private string $relativeFilePath
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getFileWithLine(): string
    {
        return $this->relativeFilePath . ':' . $this->line;
    }

    /**
     * @return array{line: int, message: string, relative_file_path: string}
     */
    public function jsonSerialize(): array
    {
        return [
            Name::LINE => $this->line,
            Name::MESSAGE => $this->message,
            Name::RELATIVE_FILE_PATH => $this->relativeFilePath,
        ];
    }

    /**
     * @param array{line: int, message: string, relative_file_path: string} $json
     */
    public static function decode(array $json): self
    {
        return new self($json[Name::LINE], $json[Name::MESSAGE], $json[Name::RELATIVE_FILE_PATH]);
    }
}
