<?php

namespace ECSPrefix20210515\Symplify\RuleDocGenerator\Contract;

use ECSPrefix20210515\Symplify\RuleDocGenerator\ValueObject\RuleDefinition;
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
    public function print(\ECSPrefix20210515\Symplify\RuleDocGenerator\Contract\CodeSampleInterface $codeSample, \ECSPrefix20210515\Symplify\RuleDocGenerator\ValueObject\RuleDefinition $ruleDefinition);
}
