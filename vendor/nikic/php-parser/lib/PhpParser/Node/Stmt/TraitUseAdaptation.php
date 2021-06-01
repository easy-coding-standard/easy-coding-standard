<?php

declare (strict_types=1);
namespace ConfigTransformer20210601\PhpParser\Node\Stmt;

use ConfigTransformer20210601\PhpParser\Node;
abstract class TraitUseAdaptation extends \ConfigTransformer20210601\PhpParser\Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
