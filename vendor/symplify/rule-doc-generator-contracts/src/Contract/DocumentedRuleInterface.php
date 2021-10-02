<?php

declare (strict_types=1);
namespace ECSPrefix20211002\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211002\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
