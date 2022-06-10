<?php

declare (strict_types=1);
namespace ECSPrefix20220610\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20220610\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
