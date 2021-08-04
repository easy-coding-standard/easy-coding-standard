<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Stmt;

use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\AttributeGroup;
class EnumCase extends \ECSPrefix20210804\PhpParser\Node\Stmt
{
    /** @var Node\Identifier Enum case name */
    public $name;
    /** @var Node\Expr|null Enum case expression */
    public $expr;
    /** @var Node\AttributeGroup[] PHP attribute groups */
    public $attrGroups;
    /**
     * @param string|Node\Identifier    $name       Enum case name
     * @param Node\Expr|null            $expr       Enum case expression
     * @param AttributeGroup[]          $attrGroups PHP attribute groups
     * @param array                     $attributes Additional attributes
     */
    public function __construct($name, \ECSPrefix20210804\PhpParser\Node\Expr $expr = null, array $attrGroups = [], array $attributes = [])
    {
        parent::__construct($attributes);
        $this->name = \is_string($name) ? new \ECSPrefix20210804\PhpParser\Node\Identifier($name) : $name;
        $this->expr = $expr;
        $this->attrGroups = $attrGroups;
    }
    public function getSubNodeNames() : array
    {
        return ['attrGroups', 'name', 'expr'];
    }
    public function getType() : string
    {
        return 'Stmt_EnumCase';
    }
}
