<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\Console\EasyCodingStandardConsoleApplication;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\ValueObject\Option;
use Symplify\PackageBuilder\Console\ShellCode;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

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
        $config = __DIR__ . '/config/config.php';
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [$config]);

        $easyCodingStandardStyle = $this->createEasyCodingStandardStyleWithBufferOutput();
        self::$container->set(EasyCodingStandardStyle::class, $easyCodingStandardStyle);

        // simulate config autowiring
        $configuration = self::$container->get(Configuration::class);
        $configFileInfo = new SmartFileInfo($config);
        $configuration->setFirstResolvedConfigFileInfo($configFileInfo);

        $this->easyCodingStandardConsoleApplication = self::$container->get(
            EasyCodingStandardConsoleApplication::class
        );
        $this->easyCodingStandardConsoleApplication->setAutoExit(false);
    }

    public function testCanPrintReport(): void
    {
        $escapedPath = addslashes(__DIR__);
        $stringInput = [
            'check',
            $escapedPath . '/wrong/wrong.php.inc',
            '--config',
            $escapedPath . '/config/config.php',
            '--' . Option::OUTPUT_FORMAT,
            JsonOutputFormatter::NAME,
        ];

        $input = new StringInput(implode(' ', $stringInput));
        $exitCode = $this->easyCodingStandardConsoleApplication->run($input);

        $output = $this->bufferedOutput->fetch();
        $this->assertStringMatchesFormatFile(__DIR__ . '/Source/expected_json_output.json', $output);
        $this->assertSame(ShellCode::ERROR, $exitCode);
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
