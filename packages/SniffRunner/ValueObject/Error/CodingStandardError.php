<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error;

use Symplify\EasyCodingStandard\Parallel\ValueObject\Name;
use ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface;
final class CodingStandardError implements \ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface
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
     * @var string
     */
    private $relativeFilePath;
    public function __construct(int $line, string $message, string $checkerClass, string $relativeFilePath)
    {
        $this->line = $line;
        $this->message = $message;
        $this->checkerClass = $checkerClass;
        $this->relativeFilePath = $relativeFilePath;
    }
    public function getLine() : int
    {
        return $this->line;
    }
    public function getMessage() : string
    {
        return $this->message;
    }
    public function getCheckerClass() : string
    {
        return $this->checkerClass;
    }
    public function getFileWithLine() : string
    {
        return $this->relativeFilePath . ':' . $this->line;
    }
    public function getRelativeFilePath() : string
    {
        return $this->relativeFilePath;
    }
    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize() : array
    {
        return [\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::LINE => $this->line, \Symplify\EasyCodingStandard\Parallel\ValueObject\Name::MESSAGE => $this->message, \Symplify\EasyCodingStandard\Parallel\ValueObject\Name::CHECKER_CLASS => $this->checkerClass, \Symplify\EasyCodingStandard\Parallel\ValueObject\Name::RELATIVE_FILE_PATH => $this->relativeFilePath];
    }
    /**
     * @param array{line: int, message: string, checker_class: string, relative_file_path: string} $json
     * @return $this
     */
    public static function decode(array $json) : \ECSPrefix20220220\Symplify\EasyParallel\Contract\SerializableInterface
    {
        return new self($json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::LINE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::MESSAGE], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::CHECKER_CLASS], $json[\Symplify\EasyCodingStandard\Parallel\ValueObject\Name::RELATIVE_FILE_PATH]);
    }
}
