<?php

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
    /**
     * @param int $kind
     * @param string $startChar
     * @param string $endChar
     */
    public function __construct($kind, $startChar, $endChar)
    {
        $this->kind = $kind;
        $this->startChar = $startChar;
        $this->endChar = $endChar;
    }
    /**
     * @return int
     */
    public function getKind()
    {
        return $this->kind;
    }
    /**
     * @return string
     */
    public function getStartChar()
    {
        return $this->startChar;
    }
    /**
     * @return string
     */
    public function getEndChar()
    {
        return $this->endChar;
    }
}
