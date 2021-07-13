<?php

declare(strict_types=1);

use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixerTest extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    /**
     * @return iterable<array<SmartFileInfo>>
     */
    public function provideData(): iterable
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixtures', '*.php.inc');
    }

    public function provideConfig(): string
    {
        return __DIR__ . '/config.php';
    }
}
