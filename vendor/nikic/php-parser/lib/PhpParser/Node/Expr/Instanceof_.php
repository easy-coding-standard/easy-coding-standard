<?php

declare (strict_types=1);
namespace ECSPrefix202607\PhpParser\Node\Expr;

use ECSPrefix202607\PhpParser\Node;
use ECSPrefix202607\PhpParser\Node\Expr;
use ECSPrefix202607\PhpParser\Node\Name;
class Instanceof_ extends Expr
{
    /** @var Expr Expression */
    public $expr;
    /** @var Name|Expr Class name */
    public $class;
    /**
     * Constructs an instanceof check node.
     *
     * @param Expr $expr Expression
     * @param Name|Expr $class Class name
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Expr $expr, Node $class, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->expr = $expr;
        $this->class = $class;
    }
    public function getSubNodeNames(): array
    {
        return ['expr', 'class'];
    }
    public function getType(): string
    {
        return 'Expr_Instanceof';
    }
}
