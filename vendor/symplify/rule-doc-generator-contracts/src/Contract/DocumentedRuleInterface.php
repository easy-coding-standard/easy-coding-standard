<?php

declare (strict_types=1);
namespace ECSPrefix202306\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202306\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
