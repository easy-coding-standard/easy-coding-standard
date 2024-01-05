<?php

declare (strict_types=1);
namespace ECSPrefix202401\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202401\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
