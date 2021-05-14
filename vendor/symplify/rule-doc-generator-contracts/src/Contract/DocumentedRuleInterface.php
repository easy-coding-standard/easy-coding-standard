<?php

namespace ECSPrefix20210514\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210514\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition();
}
