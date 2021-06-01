<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Expr;

use ConfigTransformer20210601\PhpParser\Node;
use ConfigTransformer20210601\PhpParser\Node\Expr;
use ConfigTransformer20210601\PhpParser\Node\FunctionLike;
class ArrowFunction extends \ConfigTransformer20210601\PhpParser\Node\Expr implements \ConfigTransformer20210601\PhpParser\Node\FunctionLike
{
    /** @var bool */
    public $static;
    /** @var bool */
    public $byRef;
    /** @var Node\Param[] */
    public $params = [];
    /** @var null|Node\Identifier|Node\Name|Node\NullableType|Node\UnionType */
    public $returnType;
    /** @var Expr */
    public $expr;
    /** @var Node\AttributeGroup[] */
    public $attrGroups;
    /**
     * @param array $subNodes   Array of the following optional subnodes:
     *                          'static'     => false   : Whether the closure is static
     *                          'byRef'      => false   : Whether to return by reference
     *                          'params'     => array() : Parameters
     *                          'returnType' => null    : Return type
     *                          'expr'       => Expr    : Expression body
     *                          'attrGroups' => array() : PHP attribute groups
     * @param array $attributes Additional attributes
     */
    public function __construct(array $subNodes = [], array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->static = $subNodes['static'] ?? \false;
        $this->byRef = $subNodes['byRef'] ?? \false;
        $this->params = $subNodes['params'] ?? [];
        $returnType = $subNodes['returnType'] ?? null;
        $this->returnType = \is_string($returnType) ? new \ConfigTransformer20210601\PhpParser\Node\Identifier($returnType) : $returnType;
        $this->expr = $subNodes['expr'] ?? null;
        $this->attrGroups = $subNodes['attrGroups'] ?? [];
    }
    public function getSubNodeNames() : array
    {
        return ['attrGroups', 'static', 'byRef', 'params', 'returnType', 'expr'];
    }
    public function returnsByRef() : bool
    {
        return $this->byRef;
    }
    public function getParams() : array
    {
        return $this->params;
    }
    public function getReturnType()
    {
        return $this->returnType;
    }
    public function getAttrGroups() : array
    {
        return $this->attrGroups;
    }
    /**
     * @return mixed[]|null
     */
    public function getStmts()
    {
        return [new \ConfigTransformer20210601\PhpParser\Node\Stmt\Return_($this->expr)];
    }
    public function getType() : string
    {
        return 'Expr_ArrowFunction';
    }
}
