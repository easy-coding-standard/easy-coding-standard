<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Console\EasyCodingStandardConsoleApplication;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

/**
 * @covers \Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter
 */
final class JsonOutputFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var EasyCodingStandardConsoleApplication
     */
    private $easyCodingStandardConsoleApplication;

    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/config.yml']);

        $easyCodingStandardStyle = $this->createEasyCodingStandardStyleWithBufferOutput();
        self::$container->set(EasyCodingStandardStyle::class, $easyCodingStandardStyle);

        $this->easyCodingStandardConsoleApplication = self::$container->get(
            EasyCodingStandardConsoleApplication::class
        );
        $this->easyCodingStandardConsoleApplication->setAutoExit(false);
    }

    public function testCanPrintReport(): void
    {
        $stringInput = [
            'check',
            __DIR__ . '/wrong/wrong.php.inc',
            '--config',
            __DIR__ . '/config/config.yml',
            '--' . Option::OUTPUT_FORMAT_OPTION,
            JsonOutputFormatter::NAME,
        ];

        $input = new StringInput(implode(' ', $stringInput));
        $exitCode = $this->easyCodingStandardConsoleApplication->run($input);
        $this->assertSame(ShellCode::ERROR, $exitCode);

        $output = trim($this->bufferedOutput->fetch());
        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/expected_json_output.json', $output);
    }

    /**
     * To catch printed content
     */
    private function createEasyCodingStandardStyleWithBufferOutput(): EasyCodingStandardStyle
    {
        $this->bufferedOutput = new BufferedOutput();

        return new EasyCodingStandardStyle(new StringInput(''), $this->bufferedOutput, new Terminal());
    }
}
