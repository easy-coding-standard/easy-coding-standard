<?php

declare (strict_types=1);
namespace ECSPrefix20220109\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20220109\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20220109\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
