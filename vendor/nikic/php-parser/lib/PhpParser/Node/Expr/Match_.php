<?php

declare (strict_types=1);
namespace ECSPrefix20210804\PhpParser\Node\Expr;

use ECSPrefix20210804\PhpParser\Node;
use ECSPrefix20210804\PhpParser\Node\MatchArm;
class Match_ extends \ECSPrefix20210804\PhpParser\Node\Expr
{
    /** @var Node\Expr */
    public $cond;
    /** @var MatchArm[] */
    public $arms;
    /**
     * @param MatchArm[] $arms
     */
    public function __construct(\ECSPrefix20210804\PhpParser\Node\Expr $cond, array $arms = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->cond = $cond;
        $this->arms = $arms;
    }
    public function getSubNodeNames() : array
    {
        return ['cond', 'arms'];
    }
    public function getType() : string
    {
        return 'Expr_Match';
    }
}
