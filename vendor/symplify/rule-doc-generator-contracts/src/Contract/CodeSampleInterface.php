<?php

namespace Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    /**
     * @return string
     */
    public function getGoodCode();
    /**
     * @return string
     */
    public function getBadCode();
}
