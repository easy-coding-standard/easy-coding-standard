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
     * @param \Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample
     * @param \Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition
     */
    public function print($codeSample, $ruleDefinition);
}
