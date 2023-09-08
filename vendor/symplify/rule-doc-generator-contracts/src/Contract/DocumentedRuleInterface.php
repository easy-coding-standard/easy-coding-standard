<?php

declare (strict_types=1);
namespace ECSPrefix202309\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202309\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
