<?php

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorDataCollectorSource;

abstract class SomeClass
{
    public function test(&$var)
    {
        $this->assertEquals('', '');
    }
}