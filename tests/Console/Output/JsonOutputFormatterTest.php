<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Symfony\Component\Console\Tester\ApplicationTester;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Console\Application;
use Symplify\EasyCodingStandard\Tests\AbstractConfigContainerAwareTestCase;

final class JsonOutputFormatterTest extends AbstractConfigContainerAwareTestCase
{
    /**
     * @var Application
     */
    private $application;

    protected function setUp(): void
    {
        $this->application = $this->container->get(Application::class);
    }

    public function testCanPrintReport(): void
    {
        $this->application->setAutoExit(false);
        $applicationTester = new ApplicationTester($this->application);

        $status = $applicationTester->run([
            'command' => 'check',
            'source' => [__DIR__ . '/wrong/wrong.php.inc'],
            '--config' => __DIR__ . '/config/config.yml',
            '--' . Option::OUTPUT_FORMAT_OPTION => Option::JSON_OUTPUT_FORMAT,
        ]);

        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/fixtures/json-output.json',
            $applicationTester->getDisplay()
        );
        $this->assertSame(1, $status);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config.yml';
    }
}
