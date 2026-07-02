<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Node\Stmt;

use ECSPrefix202607\PhpParser\Node;
use ECSPrefix202607\PhpParser\Node\DeclareItem;
class Declare_ extends Node\Stmt
{
    /** @var DeclareItem[] List of declares */
    public $declares;
    /** @var Node\Stmt[]|null Statements */
    public $stmts;
    /**
     * Constructs a declare node.
     *
     * @param DeclareItem[] $declares List of declares
     * @param Node\Stmt[]|null $stmts Statements
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(array $declares, ?array $stmts = null, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->declares = $declares;
        $this->stmts = $stmts;
    }
    public function getSubNodeNames(): array
    {
        return ['declares', 'stmts'];
    }
    public function getType(): string
    {
        return 'Stmt_Declare';
    }
}
