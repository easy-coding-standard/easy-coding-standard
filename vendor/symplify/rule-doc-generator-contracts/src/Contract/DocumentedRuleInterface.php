<?php

declare (strict_types=1);
namespace ECSPrefix202501\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202501\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
