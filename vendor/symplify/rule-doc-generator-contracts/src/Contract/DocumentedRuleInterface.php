<?php

declare (strict_types=1);
namespace ECSPrefix202301\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202301\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
