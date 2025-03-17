<?php

declare (strict_types=1);
namespace ECSPrefix202503\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202503\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
