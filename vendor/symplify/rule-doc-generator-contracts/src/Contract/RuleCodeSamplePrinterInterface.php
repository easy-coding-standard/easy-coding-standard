<?php

namespace Symplify\RuleDocGenerator\Contract;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
interface RuleCodeSamplePrinterInterface
{
    /**
     * @param string $class
     * @return bool
     */
    public function isMatch($class);
    /**
     * @return mixed[]
     */
    public function print(\Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample, \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
