<?php

declare (strict_types=1);
namespace ECSPrefix20210811\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210811\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20210811\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
