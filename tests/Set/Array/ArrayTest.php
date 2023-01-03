<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Array;

use Iterator;
use SplFileInfo;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyCodingStandard\Testing\PHPUnit\StaticFixtureFileFinder;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;

final class ArrayTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SplFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture');
    }

    //public function provideData(): Iterator
    //{
    //    yield StaticFixtureFileFinder::yieldFiles(__DIR__ . '/Fixture');
    //}

    public function provideConfig(): string
    {
        return __DIR__ . '/config.php';
    }
}
