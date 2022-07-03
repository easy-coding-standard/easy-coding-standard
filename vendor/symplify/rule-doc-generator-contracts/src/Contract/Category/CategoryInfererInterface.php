<?php

declare (strict_types=1);
namespace ECSPrefix202207\Symplify\RuleDocGenerator\Contract\Category;

use ECSPrefix202207\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface CategoryInfererInterface
{
    public function infer(RuleDefinition $ruleDefinition) : ?string;
}
