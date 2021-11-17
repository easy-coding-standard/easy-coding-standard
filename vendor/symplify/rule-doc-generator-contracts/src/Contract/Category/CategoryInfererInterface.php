<?php

declare (strict_types=1);
namespace ECSPrefix20211117\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20211117\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(\ECSPrefix20211117\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : ?string;
}
