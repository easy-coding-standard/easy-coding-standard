<?php

declare (strict_types=1);
namespace ECSPrefix202206\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202206\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
