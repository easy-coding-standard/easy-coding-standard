<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Expr;

use ECSPrefix20210803\PhpParser\Node\Expr;
class Clone_ extends \ECSPrefix20210803\PhpParser\Node\Expr
{
    /** @var Expr Expression */
    public $expr;
    /**
     * Constructs a clone node.
     *
     * @param Expr  $expr       Expression
     * @param array $attributes Additional attributes
     */
    public function __construct(\ECSPrefix20210803\PhpParser\Node\Expr $expr, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
    }
    public function getSubNodeNames() : array
    {
        return ['expr'];
    }
    public function getType() : string
    {
        return 'Expr_Clone';
    }
}
