<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Set\Psr12;

use Iterator;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractCheckerTestCase;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;

final class Psr12Test extends AbstractCheckerTestCase
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
        yield self::yieldFiles(__DIR__ . '/Fixture');
    }

    public function provideConfig(): string
    {
        return SetList::PSR_12;
    }
}
