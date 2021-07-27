<?php

declare (strict_types=1);
namespace ECSPrefix20210727\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210727\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(\ECSPrefix20210727\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
