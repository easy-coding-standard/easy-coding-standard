<?php

namespace Symplify\RuleDocGenerator\Contract;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    /**
     * @param string $class
     */
    public function isMatch($class) : bool;
    /**
     * @return mixed[]
     */
    public function print(\Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample, \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
