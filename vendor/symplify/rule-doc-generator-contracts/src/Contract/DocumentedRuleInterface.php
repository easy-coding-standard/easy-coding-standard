<?php

declare (strict_types=1);
namespace ECSPrefix202210\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202210\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
