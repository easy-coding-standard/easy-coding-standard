<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Testing\Contract;

interface ConfigAwareInterface
{
    public function provideConfig() : string;
}
