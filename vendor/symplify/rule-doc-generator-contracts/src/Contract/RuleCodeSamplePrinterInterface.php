<?php

declare (strict_types=1);
namespace ECSPrefix20210710\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210710\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    /**
     * @param string $class
     */
    public function isMatch($class) : bool;
    /**
     * @return string[]
     * @param \Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample
     * @param \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition
     */
    public function print($codeSample, $ruleDefinition) : array;
}
