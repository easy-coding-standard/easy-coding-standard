<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerServiceParametersShifter;

use Iterator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
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
     * @dataProvider provideShiftSniffValues()
     */
    public function test(string $inputYaml, string $expectedYaml): void
    {
        $inputContent = Yaml::parseFile($inputYaml);
        $expectedContent = Yaml::parseFile($expectedYaml);

        $this->assertSame($expectedContent, $this->checkerServiceParametersShifter->process($inputContent));
    }

    public function provideShiftSniffValues(): Iterator
    {
        yield [__DIR__ . '/Source/before/sniff.yaml', __DIR__ . '/Source/after/sniff.yaml'];
        yield [__DIR__ . '/Source/before/fixer.yaml', __DIR__ . '/Source/after/fixer.yaml'];

        // mainly "@"
        yield [__DIR__ . '/Source/before/escaping.yaml', __DIR__ . '/Source/after/escaping.yaml'];
    }
}
