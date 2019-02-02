<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Yaml\CheckerServiceParametersShifter;

use Iterator;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\Exception\DependencyInjection\Extension\InvalidSniffPropertyException;
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

    /**
     * @dataProvider provideInvalidConfigs()
     */
    public function testInvalidConfiguration(string $inputYaml, string $exceptionMessage): void
    {
        $this->expectException(InvalidSniffPropertyException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $inputContent = Yaml::parseFile($inputYaml);
        $this->checkerServiceParametersShifter->process($inputContent);
    }

    public function provideInvalidConfigs(): Iterator
    {
        yield [
            __DIR__ . '/Source/invalid/sniff_parameter_typo.yaml',
            sprintf(
                'Property "lineLimid" was not found on "%s" sniff class in configuration. Did you mean "lineLimit"?',
                LineLengthSniff::class
            ),
        ];
        /** fixer covers this internally in @see AbstractFixer::configure() */
    }
}
