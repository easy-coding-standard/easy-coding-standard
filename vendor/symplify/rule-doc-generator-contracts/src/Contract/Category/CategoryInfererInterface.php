<?php

declare (strict_types=1);
namespace ECSPrefix20210601\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210601\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(\ECSPrefix20210601\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
