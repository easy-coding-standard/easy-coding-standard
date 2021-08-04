<?php

declare (strict_types=1);
namespace ECSPrefix20210804\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210804\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20210804\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
