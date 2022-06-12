<?php

declare (strict_types=1);
namespace ECSPrefix20220612\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20220612\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
