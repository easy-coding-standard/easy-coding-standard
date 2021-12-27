<?php

declare (strict_types=1);
namespace ECSPrefix20211227\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211227\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211227\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
