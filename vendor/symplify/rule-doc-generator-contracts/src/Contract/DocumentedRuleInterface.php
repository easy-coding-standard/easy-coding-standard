<?php

declare (strict_types=1);
namespace ECSPrefix202503\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202503\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
