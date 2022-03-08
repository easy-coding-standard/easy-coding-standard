<?php

declare (strict_types=1);
namespace ECSPrefix20220308\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20220308\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
/**
 * @api
 */
interface DocumentedRuleInterface
{
    public function getRuleDefinition() : \ECSPrefix20220308\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
}
