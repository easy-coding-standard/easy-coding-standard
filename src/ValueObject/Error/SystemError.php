<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\Contract\Serializable;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;
final class SystemError implements \Symplify\EasyCodingStandard\Parallel\Contract\Serializable
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
    private $relativeFilePath;
    public function __construct(int $line, string $message, string $relativeFilePath)
    {
        $this->line = $line;
        $this->message = $message;
        $this->relativeFilePath = $relativeFilePath;
    }
    public function getMessage() : string
    {
        return $this->message;
    }
    public function getFileWithLine() : string
    {
        return $this->relativeFilePath . ':' . $this->line;
    }
    /**
     * @return array{line: int, message: string, relative_file_path: string}
     */
    public function jsonSerialize() : array
    {
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::LINE => $this->line, \Symplify\EasyCodingStandard\Parallel\ValueObject\Name::MESSAGE => $this->message, \Symplify\EasyCodingStandard\Parallel\ValueObject\Name::RELATIVE_FILE_PATH => $this->relativeFilePath];
    }
    /**
     * @param mixed[] $json
     */
    public static function decode($json) : \Symplify\EasyCodingStandard\Parallel\Contract\Serializable
    {
        return new self($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::LINE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::MESSAGE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::RELATIVE_FILE_PATH]);
    }
}
