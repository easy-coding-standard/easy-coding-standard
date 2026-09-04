<?php

declare (strict_types=1);
namespace ECSPrefix202609\PhpParser\Builder;

use ECSPrefix202609\PhpParser;
use ECSPrefix202609\PhpParser\BuilderHelpers;
use ECSPrefix202609\PhpParser\Node;
use ECSPrefix202609\PhpParser\Node\Stmt;
class Namespace_ extends Declaration
{
    /**
     * @var \PhpParser\Node\Name|null
     */
    private $name;
    /** @var Stmt[] */
    private $stmts = [];
    /**
     * Creates a namespace builder.
     *
     * @param Node\Name|string|null $name Name of the namespace
     */
    public function __construct($name)
    {
        $this->name = null !== $name ? BuilderHelpers::normalizeName($name) : null;
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
        $this->stmts[] = BuilderHelpers::normalizeStmt($stmt);
        return $this;
    }
    /**
     * Returns the built node.
     *
     * @return Stmt\Namespace_ The built node
     */
    public function getNode(): Node
    {
        return new Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
    }
}
