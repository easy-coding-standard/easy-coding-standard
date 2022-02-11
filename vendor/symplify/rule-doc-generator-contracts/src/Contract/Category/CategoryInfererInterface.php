<?php

declare (strict_types=1);
namespace ECSPrefix20220211\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20220211\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20220211\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
