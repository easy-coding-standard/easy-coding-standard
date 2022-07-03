<?php

declare (strict_types=1);
namespace ECSPrefix202207\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202207\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
