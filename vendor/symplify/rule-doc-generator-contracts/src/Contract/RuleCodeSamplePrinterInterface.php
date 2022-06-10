<?php

declare (strict_types=1);
namespace ECSPrefix20220610\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20220610\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class) : bool;
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition) : array;
}
