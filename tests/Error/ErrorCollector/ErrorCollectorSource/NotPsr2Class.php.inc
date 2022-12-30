<?php

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector\ErrorCollectorSource;

use PHPUnit\Framework\TestCase;

abstract class SomeClassTest extends TestCase
{
    public function test()
    {
        $list =
                [   1
                        ]  ;

        $this->assertEquals('', '');
    }
}
