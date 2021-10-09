<?php

declare (strict_types=1);
namespace ECSPrefix20211009\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211009\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211009\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
