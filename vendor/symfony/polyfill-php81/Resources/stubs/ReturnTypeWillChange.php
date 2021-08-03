<?php

namespace ECSPrefix20210803;

#[Attribute(\Attribute::TARGET_METHOD)]
final class ReturnTypeWillChange
{
    public function __construct()
    {
    }
}
\class_alias('ECSPrefix20210803\\ReturnTypeWillChange', 'ReturnTypeWillChange', \false);
