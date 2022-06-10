<?php

declare (strict_types=1);
namespace ECSPrefix20220610\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20220610\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
