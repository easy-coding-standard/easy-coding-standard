<?php

declare (strict_types=1);
namespace ECSPrefix20211023\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20211023\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20211023\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
