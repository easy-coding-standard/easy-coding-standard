<?php

declare (strict_types=1);
namespace ECSPrefix20210620\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210620\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20210620\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
