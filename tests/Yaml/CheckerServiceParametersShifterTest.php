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
     * @param mixed[] $config
     * @param mixed[] $expectedShiftedConfig
     */
    public function test(array $config, array $expectedShiftedConfig): void
    {
        $this->assertSame($expectedShiftedConfig, $this->checkerServiceParametersShifter->process($config));
    }

    public function provideConfigToShiftedConfig(): Iterator
    {
        yield [
            [
                'services' => [
                    LineLengthSniff::class => [
                        'absoluteLineLimit' => 'value',
                    ],
                ],
            ],
            [
                'services' => [
                    LineLengthSniff::class => [
                        'properties' => [
                            'absoluteLineLimit' => 'value',
                        ],
                    ],
                ],
            ],
        ];

        yield [
            [
                'services' => [
                    LineLengthSniff::class => [
                        'absoluteLineLimit' => null,
                    ],
                ],
            ],
            [
                'services' => [
                    LineLengthSniff::class => [
                        'properties' => [
                            'absoluteLineLimit' => null,
                        ],
                    ],
                ],
            ],
        ];
    }
}
