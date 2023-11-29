<?php

declare (strict_types=1);
namespace ECSPrefix202311\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202311\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
