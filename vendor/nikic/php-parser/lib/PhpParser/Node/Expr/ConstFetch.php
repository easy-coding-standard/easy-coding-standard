<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr;

use ECSPrefix20210804\PhpParser\Node\Expr;
use ECSPrefix20210804\PhpParser\Node\Name;
class ConstFetch extends \ECSPrefix20210804\PhpParser\Node\Expr
{
    /** @var Name Constant name */
    public $name;
    /**
     * Constructs a const fetch node.
     *
     * @param Name  $name       Constant name
     * @param array $attributes Additional attributes
     */
    public function __construct(\ECSPrefix20210804\PhpParser\Node\Name $name, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->name = $name;
    }
    public function getSubNodeNames() : array
    {
        return ['name'];
    }
    public function getType() : string
    {
        return 'Expr_ConstFetch';
    }
}
