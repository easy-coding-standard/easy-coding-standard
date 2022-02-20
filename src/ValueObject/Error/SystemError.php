<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;
use ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface;
final class SystemError implements \ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface
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
     * @param array{line: int, message: string, relative_file_path: string} $json
     * @return $this
     */
    public static function decode(array $json) : \ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface
    {
        return new self($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::LINE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::MESSAGE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::RELATIVE_FILE_PATH]);
    }
}
