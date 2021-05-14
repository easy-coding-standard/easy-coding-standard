<?php

namespace ECSPrefix20210514\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210514\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(\ECSPrefix20210514\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
