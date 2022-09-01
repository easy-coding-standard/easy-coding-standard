<?php

declare (strict_types=1);
namespace ECSPrefix202209\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202209\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
