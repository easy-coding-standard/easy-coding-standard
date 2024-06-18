<?php

declare (strict_types=1);
namespace ECSPrefix202406\Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    public function getGoodCode() : string;
    public function getBadCode() : string;
}
