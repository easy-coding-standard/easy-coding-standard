<?php

declare (strict_types=1);
namespace ECSPrefix20211010\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211010\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211010\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
