<?php

declare (strict_types=1);
namespace ECSPrefix202406\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202406\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
