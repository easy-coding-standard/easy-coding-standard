<?php

namespace Symplify\RuleDocGenerator\Contract\Category;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    /**
     * @return string|null
     * @param \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition
     */
    public function infer($ruleDefinition);
}
