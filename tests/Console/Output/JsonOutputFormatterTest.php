<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Console\Application;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Tests\AbstractConfigContainerAwareTestCase;
use Symplify\PackageBuilder\Console\ShellCode;

/**
 * @covers \Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter
 */
final class JsonOutputFormatterTest extends AbstractConfigContainerAwareTestCase
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var BufferedOutput
     */
    private $bufferedOutput;

    protected function setUp(): void
    {
        $easyCodingStandardStyle = $this->createEasyCodingStandardStyleWithBufferOutput();
        $this->container->set(EasyCodingStandardStyle::class, $easyCodingStandardStyle);

        $this->application = $this->container->get(Application::class);
        $this->application->setAutoExit(false);
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
        $exitCode = $this->application->run($input);
        $this->assertSame(ShellCode::ERROR, $exitCode);

        $output = trim($this->bufferedOutput->fetch());
        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/expected_json_output.json', $output);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config.yml';
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
