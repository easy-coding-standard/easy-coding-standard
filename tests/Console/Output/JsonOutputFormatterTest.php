<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Output;

use Nette\Utils\Json;
use Symfony\Component\Console\Tester\ApplicationTester;
use Symplify\EasyCodingStandard\Configuration\Option;
use Symplify\EasyCodingStandard\Console\Application;
use Symplify\EasyCodingStandard\Console\Output\JsonOutputFormatter;
use Symplify\EasyCodingStandard\Tests\AbstractConfigContainerAwareTestCase;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

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
            '--' . Option::OUTPUT_FORMAT_OPTION => JsonOutputFormatter::NAME,
        ]);
        $output = Json::decode($applicationTester->getDisplay(), true);

        $this->assertSame(1, $exitCode);

        $this->assertArrayHasKey('meta', $output);
        $this->assertArrayHasKey('version', $output['meta']);
        $this->assertArrayHasKey('config', $output['meta']);
        $this->assertContains($config, $output['meta']['config']);

        $this->assertArrayHasKey('totals', $output);
        $this->assertArrayHasKey('errors', $output['totals']);
        $this->assertArrayHasKey('diffs', $output['totals']);

        $this->assertArrayHasKey('files', $output);
        $this->assertArrayHasKey($sourceLocation, $output['files']);

        $this->assertArrayHasKey('line', $output['files'][$sourceLocation]['errors'][0]);
        $this->assertArrayHasKey('message', $output['files'][$sourceLocation]['errors'][0]);
        $this->assertArrayHasKey('sourceClass', $output['files'][$sourceLocation]['errors'][0]);
        $this->assertArrayHasKey('diff', $output['files'][$sourceLocation]['diffs'][0]);
        $this->assertArrayHasKey('appliedCheckers', $output['files'][$sourceLocation]['diffs'][0]);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/config/config.yml';
    }
}
