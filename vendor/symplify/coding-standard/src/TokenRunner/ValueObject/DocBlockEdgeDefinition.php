<?php

declare (strict_types=1);
namespace Symplify\CodingStandard\TokenRunner\ValueObject;

final class DocBlockEdgeDefinition
{
    /**
     * @var int
     */
    private $kind;
    /**
     * @var string
     */
    private $startChar;
    /**
     * @var string
     */
    private $endChar;
    public function __construct(int $kind, string $startChar, string $endChar)
    {
        $this->kind = $kind;
        $this->startChar = $startChar;
        $this->endChar = $endChar;
    }
    public function getKind() : int
    {
        return $this->kind;
    }
    public function getStartChar() : string
    {
        return $this->startChar;
    }
    public function getEndChar() : string
    {
        return $this->endChar;
    }
}
