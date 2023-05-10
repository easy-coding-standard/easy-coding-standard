<?php

declare (strict_types=1);
namespace ECSPrefix202305\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202305\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
