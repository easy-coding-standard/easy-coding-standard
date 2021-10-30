<?php

declare (strict_types=1);
namespace ECSPrefix20211030\Symplify\RuleDocGenerator\Contract;

interface CodeSampleInterface
{
    public function getGoodCode() : string;
    public function getBadCode() : string;
}
