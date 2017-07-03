<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\CommandLine;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

final class ConfigFilesTest extends TestCase
{
    public function test(): void
    {
        $process = $this->createProcessWithConfigAndRunIt('ConfigFilesSource/empty-config.neon');

        $this->assertSame('', $process->getErrorOutput());
        $this->assertContains('[OK] Loaded 0 checkers in total', $process->getOutput());
    }

    public function testSimpleConfig(): void
    {
        $process = $this->createProcessWithConfigAndRunIt('ConfigFilesSource/simple-config.neon');

        $this->assertSame('', $process->getErrorOutput());
        $this->assertContains('[OK] Loaded 1 checkers in total', $process->getOutput());
    }

    public function testIncludeConfig(): void
    {
        $process = $this->createProcessWithConfigAndRunIt('ConfigFilesSource/include-another-config.neon');

        $this->assertSame('', $process->getErrorOutput());
        $this->assertContains('[OK] Loaded 2 checkers in total', $process->getOutput());
    }

    private function createProcessWithConfigAndRunIt(string $config): Process
    {
        $process = new Process(sprintf(
            __DIR__ . '/../../bin/easy-coding-standard show --config %s',
            $config
        ), __DIR__);
        $process->run();

        return $process;
    }
}
