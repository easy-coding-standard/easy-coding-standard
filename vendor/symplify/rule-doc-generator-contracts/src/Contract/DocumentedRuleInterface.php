<?php

declare (strict_types=1);
namespace ECSPrefix202408\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202408\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : RuleDefinition;
}
