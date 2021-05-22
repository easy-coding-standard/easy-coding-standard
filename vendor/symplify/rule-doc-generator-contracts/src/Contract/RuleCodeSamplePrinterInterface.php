<?php

declare (strict_types=1);
namespace ECSPrefix20210522\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210522\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class) : bool;
    /**
     * @return string[]
     */
    public function print(\ECSPrefix20210522\Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample, \ECSPrefix20210522\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition) : array;
}
