<?php

declare (strict_types=1);
namespace ECSPrefix20220223\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20220223\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20220223\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
