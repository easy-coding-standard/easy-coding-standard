<?php

namespace ECSPrefix20210515\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210515\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(\ECSPrefix20210515\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
