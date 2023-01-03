<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Array;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyCodingStandard\Testing\PHPUnit\StaticFixtureFileFinder;

final class ArrayTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $this->doTestFile($filePath);
    }

    public function provideData(): Iterator
    {
        yield StaticFixtureFileFinder::yieldFiles(__DIR__ . '/Fixture');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config.php';
    }
}
