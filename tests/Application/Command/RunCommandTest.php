<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application\Command;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;

final class RunCommandTest extends TestCase
{
    public function testConfiguration(): void
    {
        $runCommand = RunCommand::createForSourceFixerAndClearCache(
            [__DIR__],
            false,
            false
        );

        $this->assertSame([__DIR__], $runCommand->getSources());
        $this->assertFalse($runCommand->isFixer());
        $this->assertFalse($runCommand->shouldClearCache());
    }
}
