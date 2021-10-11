<?php

declare (strict_types=1);
namespace ECSPrefix20211011\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211011\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211011\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
