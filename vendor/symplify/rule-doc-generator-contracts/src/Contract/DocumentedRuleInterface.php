<?php

declare (strict_types=1);
namespace ECSPrefix20211014\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211014\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20211014\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
