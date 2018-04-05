<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml;

use Iterator;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Yaml\CheckerServiceParametersShifter;

final class CheckerServiceParametersShifterTest extends TestCase
{
    /**
     * @var CheckerServiceParametersShifter
     */
    private $checkerServiceParametersShifter;

    protected function setUp(): void
    {
        $this->checkerServiceParametersShifter = new CheckerServiceParametersShifter();
    }

    /**
     * @dataProvider provideConfigToShiftedConfig()
     * @param mixed $sniffParameterValue
     * @param mixed $expectedShiftedValue
     */
    public function testSniff($sniffParameterValue, $expectedShiftedValue): void
    {
        $config = [
            'services' => [
                LineLengthSniff::class => [
                    'absoluteLineLimit' => $sniffParameterValue,
                ],
            ],
        ];

        $expectedShifterConfig = [
            'services' => [
                LineLengthSniff::class => [
                    'properties' => [
                        'absoluteLineLimit' => $expectedShiftedValue,
                    ]
                ],
            ],
        ];

        $this->assertSame($expectedShifterConfig, $this->checkerServiceParametersShifter->process($config));
    }

    public function provideConfigToShiftedConfig(): Iterator
    {
        yield ['value', 'value'];
        yield [null, null];
        yield ['@annotation', '@@annotation'];
    }
}
