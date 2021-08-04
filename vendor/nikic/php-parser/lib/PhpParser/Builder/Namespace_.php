<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Builder;

use ECSPrefix20210804\PhpParser;
use ECSPrefix20210804\PhpParser\BuilderHelpers;
use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\Stmt;
class Namespace_ extends \ECSPrefix20210804\PhpParser\Builder\Declaration
{
    private $name;
    private $stmts = [];
    /**
     * Creates a namespace builder.
     *
     * @param Node\Name|string|null $name Name of the namespace
     */
    public function __construct($name)
    {
        $this->name = null !== $name ? \ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeName($name) : null;
    }
    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $this->stmts[] = \ECSPrefix20210804\PhpParser\BuilderHelpers::normalizeStmt($stmt);
        return $this;
    }
    /**
     * Returns the built node.
     *
     * @return Stmt\Namespace_ The built node
     */
    public function getNode() : \ECSPrefix20210804\PhpParser\Node
    {
        return new \ECSPrefix20210804\PhpParser\Node\Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
    }
}
