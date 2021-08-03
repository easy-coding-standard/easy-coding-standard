<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Stmt;

use ECSPrefix20210803\PhpParser\Node;
use ECSPrefix20210803\PhpParser\Node\Expr;
class StaticVar extends \ECSPrefix20210803\PhpParser\Node\Stmt
{
    /** @var Expr\Variable Variable */
    public $var;
    /** @var null|Node\Expr Default value */
    public $default;
    /**
     * Constructs a static variable node.
     *
     * @param Expr\Variable  $var         Name
     * @param null|Node\Expr $default    Default value
     * @param array          $attributes Additional attributes
     */
    public function __construct(\ECSPrefix20210803\PhpParser\Node\Expr\Variable $var, \ECSPrefix20210803\PhpParser\Node\Expr $default = null, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->default = $default;
    }
    public function getSubNodeNames() : array
    {
        return ['var', 'default'];
    }
    public function getType() : string
    {
        return 'Stmt_StaticVar';
    }
}
