<?php

namespace ECSPrefix20210516\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210516\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    /**
     * @return \Symplify\RuleDocGenerator\ValueObject\RuleDefinition
     */
    public function getRuleDefinition();
}
