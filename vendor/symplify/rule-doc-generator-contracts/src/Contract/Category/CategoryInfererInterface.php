<?php

declare (strict_types=1);
namespace ECSPrefix20210801\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210801\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(\ECSPrefix20210801\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
