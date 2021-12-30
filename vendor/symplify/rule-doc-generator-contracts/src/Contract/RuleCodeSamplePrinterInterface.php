<?php

declare (strict_types=1);
namespace ECSPrefix20211230\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20211230\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class) : bool;
    /**
     * @return string[]
     */
    public function print(\ECSPrefix20211230\Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample, \ECSPrefix20211230\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition, bool $shouldUseConfigureMethod) : array;
}
