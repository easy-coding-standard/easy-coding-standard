<?php

declare (strict_types=1);
namespace ECSPrefix202311\Symplify\RuleDocGenerator\Contract;

use ECSPrefix202311\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    public function isMatch(string $class) : bool;
    /**
     * @return string[]
     */
    public function print(CodeSampleInterface $codeSample, RuleDefinition $ruleDefinition) : array;
}
