<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Issues;

use Iterator;
use Symplify\EasyCodingStandardTester\Testing\AbstractCheckerTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * @see https://github.com/symplify/symplify/issues/1024
 */
final class Issue1024Test extends AbstractCheckerTestCase
{
    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fileInfo): void
    {
        $this->doTestFileInfo($fileInfo);
    }

    public function provideData(): Iterator
    {
        yield [
            new SmartFileInfo(__DIR__ . '/Fixture/fixture1024.php.inc'),
            new SmartFileInfo(__DIR__ . '/Fixture/fixture1024_2.php.inc'),
        ];
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config1024.yml';
    }
}
