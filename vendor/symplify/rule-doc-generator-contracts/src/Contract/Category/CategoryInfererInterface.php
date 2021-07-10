<?php

declare (strict_types=1);
namespace ECSPrefix20210710\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix20210710\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @param \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition
     * @return string|null
     */
    public function infer($ruleDefinition);
}
