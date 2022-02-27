<?php

declare (strict_types=1);
namespace ECSPrefix20220227\Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    public function getGoodCode() : string;
    public function getBadCode() : string;
}
