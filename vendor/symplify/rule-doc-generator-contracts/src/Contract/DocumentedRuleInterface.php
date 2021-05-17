<?php

namespace ECSPrefix20210517\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210517\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition();
}
