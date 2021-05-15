<?php

namespace ECSPrefix20210515\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210515\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition();
}
