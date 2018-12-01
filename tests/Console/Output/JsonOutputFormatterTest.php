<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Nette\Utils\Json;
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
        $source = 'wrong/wrong.php.inc';
        $config = 'config/config.yml';

        $currentDir = substr(__DIR__, strlen($_SERVER['PWD'] . '/'));
        $sourceLocation = ($currentDir ? ($currentDir . '/') : '') . $source;

        $exitCode = $applicationTester->run([
            'command' => 'check',
            'source' => [__DIR__ . '/' . $source],
            '--config' => __DIR__ . '/' . $config,
            '--' . Option::OUTPUT_FORMAT_OPTION => Option::JSON_OUTPUT_FORMAT,
        ]);
        $output = Json::decode($applicationTester->getDisplay(), true);

        static::assertSame(1, $exitCode);

        static::assertArrayHasKey('meta', $output);
        static::assertArrayHasKey('version', $output['meta']);
        static::assertArrayHasKey('config', $output['meta']);
        static::assertContains($config, $output['meta']['config']);

        static::assertArrayHasKey('totals', $output);
        static::assertArrayHasKey('errors', $output['totals']);
        static::assertArrayHasKey('diffs', $output['totals']);

        static::assertArrayHasKey('files', $output);
        static::assertArrayHasKey($sourceLocation, $output['files']);

        static::assertArrayHasKey('line', $output['files'][$sourceLocation]['errors'][0]);
        static::assertArrayHasKey('message', $output['files'][$sourceLocation]['errors'][0]);
        static::assertArrayHasKey('sourceClass', $output['files'][$sourceLocation]['errors'][0]);
        static::assertArrayHasKey('diff', $output['files'][$sourceLocation]['diffs'][0]);
        static::assertArrayHasKey('appliedCheckers', $output['files'][$sourceLocation]['diffs'][0]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config.yml';
    }
}
