<?php

namespace Symplify\RuleDocGenerator\Contract\Category;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

interface CategoryInfererInterface
{
    /**
     * @return string|null
     */
    public function infer(RuleDefinition $ruleDefinition);
}
