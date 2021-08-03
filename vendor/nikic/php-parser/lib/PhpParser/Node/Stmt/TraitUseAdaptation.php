<?php

declare (strict_types=1);
namespace ECSPrefix20210803\PhpParser\Node\Stmt;

use ECSPrefix20210803\PhpParser\Node;
abstract class TraitUseAdaptation extends \ECSPrefix20210803\PhpParser\Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
