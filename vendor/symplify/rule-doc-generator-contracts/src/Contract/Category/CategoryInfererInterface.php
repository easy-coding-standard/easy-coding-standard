<?php

declare (strict_types=1);
namespace ECSPrefix202402\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202402\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
