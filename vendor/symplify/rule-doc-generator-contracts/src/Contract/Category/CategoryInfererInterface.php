<?php

declare (strict_types=1);
namespace ECSPrefix20210831\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210831\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20210831\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
