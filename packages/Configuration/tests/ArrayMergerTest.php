<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Configuration\Tests;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\ArrayMerger;

final class ArrayMergerTest extends TestCase
{
    /**
     * @dataProvider provideArrays()
     *
     * @param mixed[][] $inputArrays
     * @param mixed[] $expectedArray
     */
    public function test(array $inputArrays, array $expectedArray): void
    {
        $this->assertSame($expectedArray, ArrayMerger::mergeRecursively($inputArrays));
    }

    /**
     * @return mixed[][][]
     */
    public function provideArrays(): array
    {
        return [
            [[['key' => 'value'], ['key2' => 'value2']], ['key' => 'value', 'key2' => 'value2']],
            [[['value'], ['value2']], ['value', 'value2']],
        ];
    }
}
