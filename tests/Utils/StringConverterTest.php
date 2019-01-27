<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Utils;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Utils\StringConverter;

final class StringConverterTest extends TestCase
{
    /**
     * @var StringConverter
     */
    private $stringConverter;

    protected function setUp(): void
    {
        $this->stringConverter = new StringConverter();
    }

    /**
     * @dataProvider provideCasesForCamelCaseToUnderscore()
     */
    public function testCamelCaseToUnderscore(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->stringConverter->camelCaseToUnderscore($input));
    }

    public function provideCasesForCamelCaseToUnderscore(): Iterator
    {
        yield ['hiTom', 'hi_tom'];
    }

    /**
     * @dataProvider provideCasesForUnderscoreToCamelCase()
     */
    public function testUnderscoreToCamelCase(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->stringConverter->underscoreToCamelCase($input));
    }

    public function provideCasesForUnderscoreToCamelCase(): Iterator
    {
        yield ['hi_tom', 'hiTom'];
    }
}
