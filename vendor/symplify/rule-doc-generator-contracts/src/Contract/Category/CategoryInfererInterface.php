<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202305\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
