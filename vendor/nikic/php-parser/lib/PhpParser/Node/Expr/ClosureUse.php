<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Expr;

use ECSPrefix20210803\PhpParser\Node\Expr;
class ClosureUse extends \ECSPrefix20210803\PhpParser\Node\Expr
{
    /** @var Expr\Variable Variable to use */
    public $var;
    /** @var bool Whether to use by reference */
    public $byRef;
    /**
     * Constructs a closure use node.
     *
     * @param Expr\Variable $var        Variable to use
     * @param bool          $byRef      Whether to use by reference
     * @param array         $attributes Additional attributes
     */
    public function __construct(\ECSPrefix20210803\PhpParser\Node\Expr\Variable $var, bool $byRef = \false, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->var = $var;
        $this->byRef = $byRef;
    }
    public function getSubNodeNames() : array
    {
        return ['var', 'byRef'];
    }
    public function getType() : string
    {
        return 'Expr_ClosureUse';
    }
}
