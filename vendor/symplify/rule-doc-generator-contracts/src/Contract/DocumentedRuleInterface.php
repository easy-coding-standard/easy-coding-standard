<?php

declare (strict_types=1);
namespace ECSPrefix202406\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202406\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
